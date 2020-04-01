
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
    <div class="app-sidebar__user"><img class="app-sidebar__user-avatar " src="{{asset($avatar)}}" alt="User Image">
        <div>
            <p class="app-sidebar__user-name">{{$name}}</p>
        </div>
    </div>
    <ul class="app-menu">

        <?php
        $listRouteDashboard = ['backend_dashboard'];
        $routeAccessDashboard = \App\Core\Helpers\AuthHelper::getMultiIsAccess($listRouteDashboard);
        ?>
            @if($routeAccessDashboard['backend_dashboard'])
        <li>
            <a class="app-menu__item" href="{{route('backend_dashboard')}}">
                <i class="app-menu__icon fa fa-dashboard"></i>
                <span class="app-menu__label">Dashboard</span>
            </a>
        </li>
            @endif

            <?php
            $listElements = ['component_template','icons_template','card_template','widgets_template'];
            $routeAccessElements = \App\Core\Helpers\AuthHelper::getMultiIsAccess($listElements);
            ?>
            @if(in_array('1',$routeAccessElements))
        <li class="treeview">
            <a class="app-menu__item" href="javascript:void(0)" data-toggle="treeview">
                <i class="app-menu__icon fa fa-laptop"></i>
                <span class="app-menu__label">UI Elements</span>
                <i class="treeview-indicator fa fa-angle-right"></i>
            </a>
            <ul class="treeview-menu">
                @if($routeAccessElements['component_template'] == 1)
                <li>
                    <a class="treeview-item" href="{{route('component_template')}}">
                        <i class="icon fa fa-dot-circle-o"></i> Bootstrap Elements
                    </a>
                </li>
                @endif

                    @if($routeAccessElements['icons_template'] == 1)
                <li>
                    <a class="treeview-item" href="{{route('icons_template')}}" rel="noopener">
                        <i class="icon fa fa-dot-circle-o"></i> Font Icons
                    </a>
                </li>
                    @endif

                    @if($routeAccessElements['card_template'] == 1)
                <li>
                    <a class="treeview-item" href="{{route('card_template')}}">
                        <i class="icon fa fa-dot-circle-o"></i> Cards
                    </a>
                </li>
                    @endif

                    @if($routeAccessElements['widgets_template'] == 1)
                <li>
                    <a class="treeview-item" href="{{route('widgets_template')}}">
                        <i class="icon fa fa-dot-circle-o"></i> Widgets
                    </a>
                </li>
                    @endif
            </ul>
        </li>
            @endif

            <?php
            $listChart = ['chart_template'];
            $routeAccessChart = \App\Core\Helpers\AuthHelper::getMultiIsAccess($listChart);
            ?>
            @if($routeAccessChart['chart_template'] == 1)
        <li>
            <a class="app-menu__item" href="{{route('chart_template')}}">
                <i class="app-menu__icon fa fa-pie-chart"></i>
                <span class="app-menu__label">Charts</span>
            </a>
        </li>
            @endif

            <?php
            $listForm = ['form_component_template','custom_component_template','form_template','form_notify_template','upload_template'];
            $routeAccessForm = \App\Core\Helpers\AuthHelper::getMultiIsAccess($listForm);
            ?>
            @if(in_array('1',$routeAccessForm))
        <li class="treeview">
            <a class="app-menu__item" href="#" data-toggle="treeview">
                <i class="app-menu__icon fa fa-edit"></i>
                <span class="app-menu__label">Forms</span>
                <i class="treeview-indicator fa fa-angle-right"></i>
            </a>
            <ul class="treeview-menu">
                @if($routeAccessForm['form_component_template'] == 1)
                <li>
                    <a class="treeview-item" href="{{route('form_component_template')}}">
                        <i class="icon fa fa-dot-circle-o"></i> Form Components
                    </a>
                </li>
                @endif

                    @if($routeAccessForm['custom_component_template'] == 1)
                <li>
                    <a class="treeview-item" href="{{route('custom_component_template')}}">
                        <i class="icon fa fa-dot-circle-o"></i> Custom Components
                    </a>
                </li>
                    @endif

                    @if($routeAccessForm['form_template'] == 1)
                <li>
                    <a class="treeview-item" href="{{route('form_template')}}">
                        <i class="icon fa fa-dot-circle-o"></i> Form Samples
                    </a>
                </li>
                    @endif

                    @if($routeAccessForm['form_notify_template'] == 1)
                <li>
                    <a class="treeview-item" href="{{route('form_notify_template')}}">
                        <i class="icon fa fa-dot-circle-o"></i> Form Notifications
                    </a>
                </li>
                    @endif

                    @if($routeAccessForm['upload_template'] == 1)
                <li>
                    <a class="treeview-item" href="{{route('upload_template')}}">
                        <i class="icon fa fa-dot-circle-o"></i> Form Uploads
                    </a>
                </li>
                    @endif
            </ul>
        </li>
            @endif

            <?php
            $listExport = ['export_template'];
            $routeAccessExport = \App\Core\Helpers\AuthHelper::getMultiIsAccess($listExport);
            ?>
            @if(in_array('1',$routeAccessExport))
                <li class="treeview">
                    <a class="app-menu__item" href="#" data-toggle="treeview">
                        <i class="app-menu__icon fa fa-download"></i>
                        <span class="app-menu__label">Export/Import</span>
                        <i class="treeview-indicator fa fa-angle-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        @if($routeAccessExport['export_template'] == 1)
                            <li>
                                <a class="treeview-item" href="{{route('export_template')}}">
                                    <i class="icon fa fa-dot-circle-o"></i> Export Excel/CSV
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            <?php
            $listTable = ['table_template','data_table_template'];
            $routeAccessTable = \App\Core\Helpers\AuthHelper::getMultiIsAccess($listTable);
            ?>
            @if(in_array('1',$routeAccessTable))
                <li class="treeview">
                    <a class="app-menu__item" href="#" data-toggle="treeview">
                        <i class="app-menu__icon fa fa-th-list"></i>
                        <span class="app-menu__label">Tables</span>
                        <i class="treeview-indicator fa fa-angle-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        @if($routeAccessTable["table_template"] == 1)
                            <li>
                                <a class="treeview-item" href="{{route('table_template')}}">
                                    <i class="icon fa fa-dot-circle-o"></i> Basic Tables
                                </a>
                            </li>
                        @endif

                        @if($routeAccessTable["data_table_template"] == 1)
                            <li>
                                <a class="treeview-item" href="{{route('data_table_template')}}">
                                    <i class="icon fa fa-dot-circle-o"></i> Data Tables
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif


            <?php
            $listUser = ['backend.user.list','backend.user.add'];
            $routeAccessUser = \App\Core\Helpers\AuthHelper::getMultiIsAccess($listUser);
            ?>
            @if(in_array('1',$routeAccessUser))
                <li class="treeview">
                    <a class="app-menu__item" href="#" data-toggle="treeview">
                        <i class="app-menu__icon fa fa-users"></i>
                        <span class="app-menu__label">{{trans('backend.users')}}</span>
                        <i class="treeview-indicator fa fa-angle-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        @if($routeAccessUser['backend.user.list'] == 1)
                            <li>
                                <a class="treeview-item" href="{{route('backend.user.list')}}">
                                    <i class="icon fa fa-dot-circle-o"></i> {{trans('backend.user_list')}}
                                </a>
                            </li>
                        @endif

                        @if($routeAccessUser['backend.user.add'] == 1)
                            <li>
                                <a class="treeview-item" href="{{route('backend.user.add')}}">
                                    <i class="icon fa fa-dot-circle-o"></i> {{trans('backend.user_add')}}
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            <?php
            $listPage = ['loginPage_template','lockScreenPage_template','invoicePage_template','calendarPage_template','mailPage_template','mailPage_template','errorPage_template'];
            $routeAccessPage = \App\Core\Helpers\AuthHelper::getMultiIsAccess($listPage);
            ?>
            @if(in_array('1',$routeAccessPage))
                <li class="treeview">
                    <a class="app-menu__item" href="#" data-toggle="treeview">
                        <i class="app-menu__icon fa fa-file-text"></i>
                        <span class="app-menu__label">Pages</span>
                        <i class="treeview-indicator fa fa-angle-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        @if($routeAccessPage["loginPage_template"] == 1)
                            <li>
                                <a class="treeview-item" href="{{route('loginPage_template')}}">
                                    <i class="icon fa fa-dot-circle-o"></i> Login Page
                                </a>
                            </li>
                        @endif

                        @if($routeAccessPage["lockScreenPage_template"] == 1)
                            <li>
                                <a class="treeview-item" href="{{route('lockScreenPage_template')}}">
                                    <i class="icon fa fa-dot-circle-o"></i> Lockscreen Page
                                </a>
                            </li>
                        @endif

                        @if($routeAccessPage["invoicePage_template"] == 1)
                            <li>
                                <a class="treeview-item" href="{{route('invoicePage_template')}}">
                                    <i class="icon fa fa-dot-circle-o"></i> Invoice Page
                                </a>
                            </li>
                        @endif

                        @if($routeAccessPage["calendarPage_template"] == 1)
                            <li>
                                <a class="treeview-item" href="{{route('calendarPage_template')}}">
                                    <i class="icon fa fa-dot-circle-o"></i> Calendar Page
                                </a>
                            </li>
                        @endif

                        @if($routeAccessPage["mailPage_template"] == 1)
                            <li>
                                <a class="treeview-item" href="{{route('mailPage_template')}}">
                                    <i class="icon fa fa-dot-circle-o"></i> Mailbox
                                </a>
                            </li>
                        @endif

                        @if($routeAccessPage["errorPage_template"] == 1)
                            <li>
                                <a class="treeview-item" href="{{route('errorPage_template')}}">
                                    <i class="icon fa fa-dot-circle-o"></i> Error Page
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif
    </ul>
    <div class="sidebar-footer">
        <a data-toggle="tooltip" class="profile_user" data-placement="top" title="" data-original-title="@lang('common.profile')" href="{{route('backend.user.profile')}}">
            <span class="fa fa-cog" aria-hidden="true"></span>
        </a>
        <a data-toggle="tooltip" class="logout-page" data-placement="top" title="" data-original-title="@lang('common.logout')" >
            <span class="fa fa-power-off" aria-hidden="true"></span>
        </a>
    </div>
</aside>
