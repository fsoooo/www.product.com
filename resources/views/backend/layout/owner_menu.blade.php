<li>
    <a href="{{ url('backend/order') }}">
        订单管理
    </a>
</li>

<?php
$active = preg_match("/\/product\//",Request::getPathinfo()) ? "active open" : "";
?>
<li>
    <a href="#" class="dropdown-toggle">
        财务统计
        <i class="fa fa-chevron-circle-right drop-icon"></i>
    </a>
    <ul class="submenu">
        <li>
            <a href="<?php echo e(asset('backend/brokerage')); ?>">
                佣金统计
            </a>
        </li>
    </ul>
</li>