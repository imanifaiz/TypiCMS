        <div class="btn-toolbar" role="toolbar">
            <div class="btn-group pull-right">
                @section('btn-locales')
                    @foreach (Config::get('app.locales') as $locale)
                        <a class="btn btn-default btn-xs @if($locale == Config::get('app.locale')) active @endif" href="?locale={{ $locale }}">@lang('global.languages.'.$locale)</a>
                    @endforeach
                @show
            </div>
        </div>
