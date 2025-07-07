{{-- @php(the_content())

@if ($pagination())
  <nav class="page-nav" aria-label="Page">
    {!! $pagination !!}
  </nav>
@endif --}}
<div class="page-content py-20">
  <div class="container">
    <div>
      @php(the_content())
    </div>
  </div>
</div>
