<?php
namespace Wandu\Foundation\Bridges;

use Throwable;
use Wandu\Http\Psr\Response;
use Wandu\Http\Psr\Stream\StringStream;
use Whoops\Exception\Formatter;
use Whoops\Exception\Inspector;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Whoops\Util\Misc;
use Whoops\Util\TemplateHelper;

class WhoopsToPsr7 extends PrettyPageHandler
{
    /**
     * @param \Throwable $exception
     * @return string
     */
    public function responsify(Throwable $exception)
    {
        $this->setRun(new Run());
        $this->setException($exception);
        $this->setInspector(new Inspector($exception));

        $helper = new TemplateHelper();
        
        $templateFile = $this->getResource("views/layout.html.php");
        $cssFile      = $this->getResource("css/whoops.base.css");
        $zeptoFile    = $this->getResource("js/zepto.min.js");
        $clipboard    = $this->getResource("js/clipboard.min.js");
        $jsFile       = $this->getResource("js/whoops.base.js");

        $inspector = $this->getInspector();
        $frames    = $inspector->getFrames();

        $code = $inspector->getException()->getCode();

        if ($inspector->getException() instanceof \ErrorException) {
            // ErrorExceptions wrap the php-error types within the "severity" property
            $code = Misc::translateErrorCode($inspector->getException()->getSeverity());
        }

        // List of variables that will be passed to the layout template.
        $vars = array(
            "page_title" => $this->getPageTitle(),

            // @todo: Asset compiler
            "stylesheet" => file_get_contents($cssFile),
            "zepto"      => file_get_contents($zeptoFile),
            "clipboard"  => file_get_contents($clipboard),
            "javascript" => file_get_contents($jsFile),

            // Template paths:
            "header"      => $this->getResource("views/header.html.php"),
            "frame_list"  => $this->getResource("views/frame_list.html.php"),
            "frame_code"  => $this->getResource("views/frame_code.html.php"),
            "env_details" => $this->getResource("views/env_details.html.php"),

            "title"          => $this->getPageTitle(),
            "name"           => explode("\\", $inspector->getExceptionName()),
            "message"        => $inspector->getException()->getMessage(),
            "code"           => $code,
            "plain_exception" => Formatter::formatExceptionPlain($inspector),
            "frames"         => $frames,
            "has_frames"     => !!count($frames),
            "handler"        => $this,
            "handlers"       => $this->getRun()->getHandlers(),

            "tables"      => array(
                "GET Data"              => $_GET,
                "POST Data"             => $_POST,
                "Files"                 => $_FILES,
                "Cookies"               => $_COOKIE,
                "Session"               => isset($_SESSION) ? $_SESSION :  array(),
                "Server/Request Data"   => $_SERVER,
                "Environment Variables" => $_ENV,
            ),
        );

        // Add extra entries list of data tables:
        // @todo: Consolidate addDataTable and addDataTableCallback
        $extraTables = array_map(function ($table) {
            return $table instanceof \Closure ? $table() : $table;
        }, $this->getDataTables());
        $vars["tables"] = array_merge($extraTables, $vars["tables"]);

        $helper->setVariables($vars);
        ob_start();
        $helper->render($templateFile);
        $contents = ob_get_contents();
        ob_end_clean();
        
        return new Response(500, new StringStream($contents));
    }
}
