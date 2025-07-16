<!doctype html>
<html @php(language_attributes())>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php(do_action('get_header'))
    @php(wp_head())

    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>

  <body @php(body_class('min-h-screen'))>
    @php(wp_body_open())

    <div id="app" class="min-h-screen flex flex-col">

      @include('partials.header')

      <div class="flex-grow">
        <main id="main" class="main">
          @yield('content')
        </main>

        @hasSection('sidebar')
          <aside class="sidebar">
            @yield('sidebar')
          </aside>
        @endif
      </div>

      @include('partials.footer')
    </div>

    @php(do_action('get_footer'))
    @php(wp_footer())

    <x-alert />

  </body>
</html>
