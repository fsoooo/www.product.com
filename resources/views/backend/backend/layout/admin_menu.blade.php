<?php
$active = preg_match("/\/role\//",Request::getPathinfo()) ? "active open" : "";
?>
<li class="{{$active}}">
    <a href="#" class="dropdown-toggle">
        <i class="fa fa-desktop"></i>
        <span>权限管理</span>
        <i class="fa fa-chevron-circle-right drop-icon"></i>
    </a>
    <ul class="submenu">
        <li>
            <a href="{{url('backend/role/roles')}}">
                角色管理
            </a>
        </li>
        <li>
            <a href="{{url('backend/role/permissions')}}">
                权限管理
            </a>
        </li>
        <li>
            <a href="{{url('backend/role/role_bind_permission')}}">
                角色权限关联
            </a>
        </li>
        <li>
            <a href="{{url('backend/role/user_bind_roles')}}">
                账户角色关联
            </a>
        </li>
    </ul>
</li>