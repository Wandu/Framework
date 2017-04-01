<?php
/** @var \Wandu\View\Phiew\Template $this */
?>
<html>
<head>
    <title><?=$title?></title>
<?=$this->content('styles')?>
<?=$this->content('nothing')?>
</head>
<body>
<?=$this->content('contents')?>
<footer><?=$version?></footer>
<?=$this->content('scripts')?>
</body>
</html>
