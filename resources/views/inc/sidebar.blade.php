<ul class="metismenu cm-menu-items" id="sidebar-menu">
    @if(Session::get('dashboard')=='active')
    <li>
        <a href="{{route('dashboard')}}" data-level="1"><i class="fas fa-fw fa-lg fa-home"></i> Dashboard</a>
    </li>
    @endif
    @if(Session::get('purchasermanagement')=='active' || Session::get('packagemanagement')=='active' || Session::get('bonusmanagement')=='active')
        <li>
            <a class="has-arrow">
                <i class="fas fa-coins fa-fw fa-lg"></i> Edcoin
            </a>
            <ul class="mm-collapse">
                @if(Session::get('purchasermanagement')=='active')
                    <li>
                        <a href="{{route('purchaser.index')}}" data-level="2">
                            <i class="fas fa-fw fa-user-friends"></i> Purchaser Management
                        </a>
                    </li>
                @endif
                @if(Session::get('bonusmanagement')=='active')
                    <li>
                        <a href="{{route('bonus.index')}}" data-level="2">
                            <i class="fas fa-fw fa-gift"></i> Bonus Management
                        </a>
                    </li>
                @endif

                @if(Session::get('downloadreports')=='active')
                    <li>
                        <a href="{{route('download.report')}}" data-level="2">
                            <i class="fas fa-fw fa-newspaper"></i> Download Reports
                        </a>
                    </li>
                @endif

                @if(Session::get('edcoinratelogs')=='active'||Session::get('referralbonuslogs')=='active'||Session::get('transactionlogs')=='active')
                <li>
                    <a class="has-arrow">
                        <i class="fas fa-fw fa-align-justify"></i> Activity Logs
                    </a>
                    <ul class="mm-collapse">
                        @if(Session::get('referralbonuslogs')=='active')
                            <li>
                                <a href="{{route('bonus.activity')}}" data-level="3">
                                    <i class="fas fa-fw fa-gift"></i> Referral Bonus Logs
                                </a>
                            </li>
                        @endif
                        @if(Session::get('transactionlogs')=='active')
                            <li>
                                <a href="{{route('transaction.index')}}" data-level="3">
                                  <i class="fas fa-fw fa-list"></i> Transaction Logs
                                </a>
                            </li>
                        @endif
                        @if(Session::get('edcoinratelogs')=='active')
                            <li>
                                <a href="{{route('edcoin.activity')}}" data-level="3">
                                  <i class="fas fa-fw fa-coins"></i> EDCOIN Rate Logs
                                </a>
                            </li>
                        @endif
                        <!-- @if(Session::get('currencyratelogs')=='active')
                            <li>
                                <a href="{{route('currency.activity')}}" data-level="3">
                                  <i class="fas fa-fw fa-money-bill-alt"></i> Currency Rate Logs
                                </a>
                            </li>
                        @endif -->
                    </ul>
                </li>
                @endif
            </ul>
        </li>
    @endif
    @if(Session::get('usermanagement')=='active' || Session::get('rolemanagement')=='active' || Session::get('menumanagement')=='active' || Session::get('permissionmanagement')=='active')
        <li>
            <a class="has-arrow">
                <i class="fas fa-cog fa-fw fa-lg"></i> User Settings
            </a>
            <ul class="mm-collapse">
                @if(Session::get('usermanagement')=='active')
                    <li><a href="{{ route('users.index') }}" data-level="2"><i class="fa fa-fw fa-users"></i> User Management</a></li>
                @endif
                @if(Session::get('rolemanagement')=='active')
                    <li><a href="{{ route('role.index') }}" data-level="2"><i class="fa fa-fw  fa-book"></i> Role Management</a></li>
                @endif
                @if(Session::get('menumanagement')=='active')
                    <li><a href="{{ route('menu.index') }}" data-level="2"><i class="fa fa-fw fa-list-alt"></i> Menu Management</a>
                    </li>
                @endif
                @if(Session::get('permissionmanagement')=='active')
                    <li><a href="{{ route('permission.index') }}" data-level="2"><i class="fa fa-fw fa-key"></i> Permission Management</a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
    @if(Session::get('countrymanagement')=='active')
        <li>
            <a class="has-arrow">
                <i class="fas fa-toolbox fa-fw fa-lg"></i> Master Setup
            </a>
            <ul class="mm-collapse">
                @if(Session::get('countrymanagement')=='active')
                    <li><a href="{{ route('country.index') }}" data-level="2"><i class="fas fa-fw fa-globe-africa"></i> Country Management</a></li>
                @endif
            </ul>
        </li>
    @endif
</ul>
