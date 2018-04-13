<div id="nav-col">
    <section id="col-left" class="col-left-nano">
        <div id="col-left-inner" class="col-left-nano-content">
            <div id="user-left-box" class="clearfix hidden-sm hidden-xs">
                <img alt="" src="/r_backend/img/samples/head.jpg"/>
                <div class="user-box">
                                <span class="name">
                                    {{Auth::guard('admin')->user()->name}}<br />
                                    {{Auth::guard('admin')->user()->display_name}}
                                </span>
                                <span class="status">
                                    <i class="fa fa-circle"></i> Online
                                </span>
                </div>
            </div>
            <?php
            if(!Cache::has('user_roles_array' . \Auth::guard('admin')->user()->email)){
                $roles = array();
                $r = Auth::guard('admin')->user()->roles;
                foreach($r as $k => $v){
                    $roles[] = $v->name;
                }
                $expiresAt = Carbon\Carbon::now()->addMinutes(config('session.lifetime'));
                \Cache::put('user_roles_array' .  Auth::guard('admin')->user()->email, $roles, $expiresAt);
            }
            $roles = Cache::get('user_roles_array' . Auth::guard('admin')->user()->email)
            ?>
            <div class="collapse navbar-collapse navbar-ex1-collapse" id="sidebar-nav">
                <ul class="nav nav-pills nav-stacked">
                    {{--管理员菜单--}}
                    @if(in_array('admin',$roles))
                        @include('backend.layout.admin_menu')
                    @endif
                    {{--平台所属者菜单--}}
                    @if(in_array('owner', $roles))
                        @include('backend.layout.owner_menu')
                    @endif
                    {{--业管专员菜单--}}
                    @if(in_array('worker', $roles))
                        @include('backend.layout.worker_menu')
                    @endif
                </ul>
            </div>
        </div>
    </section>
</div>
