@if ($paginator->hasPages())
<?php
if(isset($_REQUEST['categories_id'])){
   $url_category = $_REQUEST['categories_id'];
}
else{
   $url_category = '';
}

?>
   <ul class="pagination">
       {{-- Previous Page Link --}}
       @if ($paginator->onFirstPage())
           <li class="disabled"><span>&laquo;</span></li>
       @else
           <li><a href="{{ $paginator->previousPageUrl() }}{{(!empty($url_category)) ? '&categories_id='.$url_category : ''}}" rel="prev">&laquo;</a></li>
       @endif

       {{-- Pagination Elements --}}
       @foreach ($elements as $element)
           {{-- "Three Dots" Separator --}}
           @if (is_string($element))
               <li class="disabled"><span>{{ $element }}</span></li>
           @endif

           {{-- Array Of Links --}}
           @if (is_array($element))
               @foreach ($element as $page => $url)
                   @if ($page == $paginator->currentPage())
                       <li class="active"><span>{{ $page }}</span></li>
                   @else
                       <li><a href="{{ $url }}{{(!empty($url_category)) ? '&categories_id='.$url_category : ''}}">{{ $page }}</a></li>
                   @endif
               @endforeach
           @endif
       @endforeach

       {{-- Next Page Link --}}
       @if ($paginator->hasMorePages())
           <li><a href="{{ $paginator->nextPageUrl() }}{{(!empty($url_category)) ? '&categories_id='.$url_category : ''}}" rel="next">&raquo;</a></li>
       @else
           <li class="disabled"><span>&raquo;</span></li>
       @endif
   </ul>
@endif