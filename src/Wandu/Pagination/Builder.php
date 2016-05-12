<?php
namespace Festiv\Pagination;

use Wandu\Http\Contracts\QueryParamsInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Psr\Http\Message\ServerRequestInterface;

use Wandu\Laravel\Repository\PaginationRepositoryInterface;

class Builder
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Wandu\Http\Contracts\QueryParamsInterface $queries
     */
    public function __construct(
        ServerRequestInterface $request,
        QueryParamsInterface $queries
    ) {
        $this->request = $request;
        $this->queries = $queries;
    }

    /**
     * @param \Wandu\Laravel\Repository\PaginationRepositoryInterface $repository
     * @param int $perPageDefault
     * @param array $appends
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function build(
        PaginationRepositoryInterface $repository,
        $perPageDefault = 10,
        array $appends = []
    ) {
        $page = $this->queries->get('page', 1, 'int');
        $perPage = $this->queries->get('per_page', $perPageDefault, 'int');
        $count = $repository->countAll();
        $items = $repository->getItems($perPage * ($page-1), $perPage);
        $pagination = new LengthAwarePaginator($items, $count, $perPage, $page);
        $pagination->setPath($this->request->getUri()->getPath());

        foreach ($appends as $key => $value) {
            $pagination = $pagination->appends($key, $value);
        }

        if ($perPage === $perPageDefault) {
            return $pagination;
        }
        return $pagination->appends('per_page', $perPage);
    }
}
