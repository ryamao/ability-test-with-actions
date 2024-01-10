<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\Component;

class PageLinks extends Component
{
    public bool $hasLeftDots = false;
    public bool $hasRightDots = false;
    public array $leftPages = [];
    public array $centerPages = [];
    public array $rightPages = [];

    /**
     * Create a new component instance.
     */
    public function __construct(public LengthAwarePaginator $paginator)
    {
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();

        if ($lastPage <= 5) {
            $this->leftPages = range(1, $lastPage);
        } else {
            $this->hasLeftDots = $currentPage >= 4;
            $this->hasRightDots = $currentPage <= $lastPage - 3;

            if ($this->hasLeftDots) {
                $this->leftPages = [1];
            } else {
                $this->leftPages = range(1, 3);
            }

            if ($this->hasRightDots) {
                $this->rightPages = [$lastPage];
            } else {
                $this->rightPages = range($lastPage - 2, $lastPage);
            }

            if ($this->hasLeftDots && $this->hasRightDots) {
                $this->centerPages = [$currentPage];
            }
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.page-links');
    }
}
