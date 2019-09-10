<div class="uk-card uk-card-default">
    <div class="uk-card-body">
        <ul class="uk-nav uk-nav-default" uk-nav>
            @foreach (config('menu') as $i => $menu)
                @if (auth('officer')->user()->hasPermissions(collect($menu['menu'])->pluck('permission')))
                    @if ($i > 0)
                        <li class="uk-nav-divider"></li>
                    @endif
                    
                    <li class="uk-nav-header uk-margin-remove">{{ $menu['header'] }}</li>

                    @foreach ($menu['menu'] as $item)
                        @if (isset($item['permission']) && auth('officer')->user()->hasPermission($item['permission']))
                            <li>
                                <a href="{{ url($item['link']) }}">{{ $item['title'] }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            <li class="uk-nav-divider"></li>
            <li>
                <a href="{{ url('/admin/logout') }}">Sign Out</a>
            </li>
        </ul>
    </div>
</div>