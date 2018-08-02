@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                @include('backend.layout.alert_info')
                <div class="row">
                    <div class="col-lg-12">
                        <ol class="breadcrumb">
                            <li><a href="#">Home</a></li>
                            <li class="active"><span>Dashboard</span></li>
                        </ol>
                        <h1>Dashboard</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-sm-6 col-xs-12">
                        <div class="main-box infographic-box">
                            <i class="fa fa-user red-bg"></i>
                            <span class="headline">Users</span>
                                    <span class="value">
                                        <span class="timer" data-from="120" data-to="2562" data-speed="1000" data-refresh-interval="50">
                                            256
                                        </span>
                                    </span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-xs-12">
                        <div class="main-box infographic-box">
                            <i class="fa fa-shopping-cart emerald-bg"></i>
                            <span class="headline">Purchases</span>
                                    <span class="value">
                                        <span class="timer" data-from="30" data-to="658" data-speed="800" data-refresh-interval="30">
                                            658
                                        </span>
                                    </span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-xs-12">
                        <div class="main-box infographic-box">
                            <i class="fa fa-money green-bg"></i>
                            <span class="headline">Income</span>
                                    <span class="value">&#36;
                                        <span class="timer" data-from="83" data-to="8400" data-speed="900" data-refresh-interval="60">
                                            8400
                                        </span>
                                    </span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-xs-12">
                        <div class="main-box infographic-box">
                            <i class="fa fa-eye yellow-bg"></i>
                            <span class="headline">Monthly Visits</span>
                                    <span class="value">
                                        <span class="timer" data-from="539" data-to="12526" data-speed="1100">
                                            12526
                                        </span>
                                    </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-9 col-lg-10">
                        <div class="main-box">
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="graph-box emerald-bg">
                                        <h2>Sales &amp; Earnings</h2>
                                        <div class="graph" id="graph-line" style="max-height: 335px;"></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="row graph-nice-legend">
                                        <div class="graph-legend-row col-md-12 col-sm-4">
                                            <div class="graph-legend-row-inner">
                                                        <span class="graph-legend-name">
                                                            Earnings
                                                        </span>
                                                        <span class="graph-legend-value">
                                                            &dollar;94.382
                                                        </span>
                                            </div>
                                        </div>
                                        <div class="graph-legend-row col-md-12 col-sm-4">
                                            <div class="graph-legend-row-inner">
                                                        <span class="graph-legend-name">
                                                        Orders
                                                        </span>
                                                        <span class="graph-legend-value">
                                                        3.930
                                                        </span>
                                            </div>
                                        </div>
                                        <div class="graph-legend-row col-md-12 col-sm-4">
                                            <div class="graph-legend-row-inner">
                                                        <span class="graph-legend-name">
                                                        New Clients
                                                        </span>
                                                        <span class="graph-legend-value">
                                                        894
                                                        </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <div class="social-box-wrapper">
                            <div class="social-box col-md-12 col-sm-4 facebook">
                                <i class="fa fa-facebook"></i>
                                <div class="clearfix">
                                    <span class="social-count">184k</span>
                                    <span class="social-action">likes</span>
                                </div>
                                <span class="social-name">facebook</span>
                            </div>
                            <div class="social-box col-md-12 col-sm-4 twitter">
                                <i class="fa fa-twitter"></i>
                                <div class="clearfix">
                                    <span class="social-count">49k</span>
                                    <span class="social-action">tweets</span>
                                </div>
                                <span class="social-name">twitter</span>
                            </div>
                            <div class="social-box col-md-12 col-sm-4 google">
                                <i class="fa fa-google-plus"></i>
                                <div class="clearfix">
                                    <span class="social-count">204</span>
                                    <span class="social-action">circles</span>
                                </div>
                                <span class="social-name">google+</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix">
                            <header class="main-box-header clearfix">
                                <h2 class="pull-left">Last orders</h2>
                                <div class="filter-block pull-right">
                                    <div class="form-group pull-left">
                                        <input type="text" class="form-control" placeholder="Search...">
                                        <i class="fa fa-search search-icon"></i>
                                    </div>
                                    <a href="#" class="btn btn-primary pull-right">
                                        <i class="fa fa-eye fa-lg"></i> View all orders
                                    </a>
                                </div>
                            </header>
                            <div class="main-box-body clearfix">
                                <div class="table-responsive clearfix">
                                    <table class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th><a href="#"><span>Order ID</span></a></th>
                                            <th><a href="#" class="desc"><span>Date</span></a></th>
                                            <th><a href="#" class="asc"><span>Customer</span></a></th>
                                            <th class="text-center"><span>Status</span></th>
                                            <th class="text-right"><span>Price</span></th>
                                            <th>&nbsp;</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>
                                                <a href="#">#8002</a>
                                            </td>
                                            <td>
                                                2013/08/08
                                            </td>
                                            <td>
                                                <a href="#">Robert De Niro</a>
                                            </td>
                                            <td class="text-center">
                                                <span class="label label-success">Completed</span>
                                            </td>
                                            <td class="text-right">
                                                &dollar; 825.50
                                            </td>
                                            <td class="text-center" style="width: 15%;">
                                                <a href="#" class="table-link">
                                                            <span class="fa-stack">
                                                                <i class="fa fa-square fa-stack-2x"></i>
                                                                <i class="fa fa-search-plus fa-stack-1x fa-inverse"></i>
                                                            </span>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <a href="#">#5832</a>
                                            </td>
                                            <td>
                                                2013/08/08
                                            </td>
                                            <td>
                                                <a href="#">John Wayne</a>
                                            </td>
                                            <td class="text-center">
                                                <span class="label label-warning">On hold</span>
                                            </td>
                                            <td class="text-right">
                                                &dollar; 923.93
                                            </td>
                                            <td class="text-center" style="width: 15%;">
                                                <a href="#" class="table-link">
                                                            <span class="fa-stack">
                                                                <i class="fa fa-square fa-stack-2x"></i>
                                                                <i class="fa fa-search-plus fa-stack-1x fa-inverse"></i>
                                                            </span>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <a href="#">#2547</a>
                                            </td>
                                            <td>
                                                2013/08/08
                                            </td>
                                            <td>
                                                <a href="#">Anthony Hopkins</a>
                                            </td>
                                            <td class="text-center">
                                                <span class="label label-info">Pending</span>
                                            </td>
                                            <td class="text-right">
                                                &dollar; 1.625.50
                                            </td>
                                            <td class="text-center" style="width: 15%;">
                                                <a href="#" class="table-link">
                                                            <span class="fa-stack">
                                                            <i class="fa fa-square fa-stack-2x"></i>
                                                            <i class="fa fa-search-plus fa-stack-1x fa-inverse"></i>
                                                            </span>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <a href="#">#9274</a>
                                            </td>
                                            <td>
                                                2013/08/08
                                            </td>
                                            <td>
                                                <a href="#">Charles Chaplin</a>
                                            </td>
                                            <td class="text-center">
                                                <span class="label label-danger">Cancelled</span>
                                            </td>
                                            <td class="text-right">
                                                &dollar; 35.34
                                            </td>
                                            <td class="text-center" style="width: 15%;">
                                                <a href="#" class="table-link">
                                                            <span class="fa-stack">
                                                            <i class="fa fa-square fa-stack-2x"></i>
                                                            <i class="fa fa-search-plus fa-stack-1x fa-inverse"></i>
                                                            </span>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <a href="#">#8463</a>
                                            </td>
                                            <td>
                                                2013/08/08
                                            </td>
                                            <td>
                                                <a href="#">Gary Cooper</a>
                                            </td>
                                            <td class="text-center">
                                                <span class="label label-success">Completed</span>
                                            </td>
                                            <td class="text-right">
                                                &dollar; 34.199.99
                                            </td>
                                            <td class="text-center" style="width: 15%;">
                                                <a href="#" class="table-link">
                                                            <span class="fa-stack">
                                                            <i class="fa fa-square fa-stack-2x"></i>
                                                            <i class="fa fa-search-plus fa-stack-1x fa-inverse"></i>
                                                            </span>
                                                </a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="main-box small-graph-box red-bg">
                            <span class="value">2.562</span>
                            <span class="headline">Users</span>
                            <div class="progress">
                                <div style="width: 60%;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class="progress-bar">
                                    <span class="sr-only">60% Complete</span>
                                </div>
                            </div>
                                    <span class="subinfo">
                                        <i class="fa fa-arrow-circle-o-up"></i> 10% higher than last week
                                    </span>
                                    <span class="subinfo">
                                        <i class="fa fa-users"></i> 29 new users
                                    </span>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="main-box small-graph-box emerald-bg">
                            <span class="value">69.600</span>
                            <span class="headline">Visits</span>
                            <div class="progress">
                                <div style="width: 84%;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="84" role="progressbar" class="progress-bar">
                                    <span class="sr-only">84% Complete</span>
                                </div>
                            </div>
                                    <span class="subinfo">
                                        <i class="fa fa-arrow-circle-o-down"></i> 22% less than last week
                                    </span>
                                    <span class="subinfo">
                                        <i class="fa fa-globe"></i> 84.912 last week
                                    </span>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12 hidden-sm">
                        <div class="main-box small-graph-box green-bg">
                            <span class="value">923</span>
                            <span class="headline">Orders</span>
                            <div class="progress">
                                <div style="width: 42%;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="42" role="progressbar" class="progress-bar">
                                    <span class="sr-only">42% Complete</span>
                                </div>
                            </div>
                                    <span class="subinfo">
                                        <i class="fa fa-arrow-circle-o-up"></i> 15% higher than last week
                                    </span>
                                    <span class="subinfo">
                                        <i class="fa fa-shopping-cart"></i> 8 new orders
                                    </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="main-box clearfix">
                            <header class="main-box-header clearfix">
                                <h2>Conversation</h2>
                            </header>
                            <div class="main-box-body clearfix">
                                <div class="conversation-wrapper">
                                    <div class="conversation-content">
                                        <div class="conversation-inner">
                                            <div class="conversation-item item-left clearfix">
                                                <div class="conversation-user">
                                                    <img src="/r_backend/img/samples/ryan.png" alt=""/>
                                                </div>
                                                <div class="conversation-body">
                                                    <div class="name">
                                                        Ryan Gossling
                                                    </div>
                                                    <div class="time hidden-xs">
                                                        September 21, 2013 18:28
                                                    </div>
                                                    <div class="text">
                                                        I don't think they tried to market it to the billionaire, spelunking,
                                                        base-jumping crowd.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="conversation-item item-right clearfix">
                                                <div class="conversation-user">
                                                    <img src="/r_backend/img/samples/kunis.png" alt=""/>
                                                </div>
                                                <div class="conversation-body">
                                                    <div class="name">
                                                        Mila Kunis
                                                    </div>
                                                    <div class="time hidden-xs">
                                                        September 21, 2013 12:45
                                                    </div>
                                                    <div class="text">
                                                        The path of the righteous man is beset on all sides by the iniquities of the
                                                        selfish and the tyranny of evil men. Blessed is he who, in the name of charity and
                                                        good will.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="conversation-item item-right clearfix">
                                                <div class="conversation-user">
                                                    <img src="/r_backend/img/samples/kunis.png" alt=""/>
                                                </div>
                                                <div class="conversation-body">
                                                    <div class="name">
                                                        Mila Kunis
                                                    </div>
                                                    <div class="time hidden-xs">
                                                        September 21, 2013 12:45
                                                    </div>
                                                    <div class="text">
                                                        The path of the righteous man is beset on all sides by the iniquities of the
                                                        selfish and the tyranny of evil men. Blessed is he who, in the name of charity and
                                                        good will.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="conversation-item item-left clearfix">
                                                <div class="conversation-user">
                                                    <img src="/r_backend/img/samples/ryan.png" alt=""/>
                                                </div>
                                                <div class="conversation-body">
                                                    <div class="name">
                                                        Ryan Gossling
                                                    </div>
                                                    <div class="time hidden-xs">
                                                        September 21, 2013 18:28
                                                    </div>
                                                    <div class="text">
                                                        I don't think they tried to market it to the billionaire, spelunking,
                                                        base-jumping crowd.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="conversation-item item-right clearfix">
                                                <div class="conversation-user">
                                                    <img src="/r_backend/img/samples/kunis.png" alt=""/>
                                                </div>
                                                <div class="conversation-body">
                                                    <div class="name">
                                                        Mila Kunis
                                                    </div>
                                                    <div class="time hidden-xs">
                                                        September 21, 2013 12:45
                                                    </div>
                                                    <div class="text">
                                                        The path of the righteous man is beset on all sides by the iniquities of the
                                                        selfish and the tyranny of evil men. Blessed is he who, in the name of charity and
                                                        good will.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="conversation-item item-right clearfix">
                                                <div class="conversation-user">
                                                    <img src="/r_backend/img/samples/kunis.png" alt=""/>
                                                </div>
                                                <div class="conversation-body">
                                                    <div class="name">
                                                        Mila Kunis
                                                    </div>
                                                    <div class="time hidden-xs">
                                                        September 21, 2013 12:45
                                                    </div>
                                                    <div class="text">
                                                        The path of the righteous man is beset on all sides by the iniquities of the
                                                        selfish and the tyranny of evil men. Blessed is he who, in the name of charity and
                                                        good will.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="conversation-item item-left clearfix">
                                                <div class="conversation-user">
                                                    <img src="/r_backend/img/samples/ryan.png" alt=""/>
                                                </div>
                                                <div class="conversation-body">
                                                    <div class="name">
                                                        Ryan Gossling
                                                    </div>
                                                    <div class="time hidden-xs">
                                                        September 21, 2013 18:28
                                                    </div>
                                                    <div class="text">
                                                        I don't think they tried to market it to the billionaire, spelunking,
                                                        base-jumping crowd.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="conversation-item item-right clearfix">
                                                <div class="conversation-user">
                                                    <img src="/r_backend/img/samples/kunis.png" alt=""/>
                                                </div>
                                                <div class="conversation-body">
                                                    <div class="name">
                                                        Mila Kunis
                                                    </div>
                                                    <div class="time hidden-xs">
                                                        September 21, 2013 12:45
                                                    </div>
                                                    <div class="text">
                                                        The path of the righteous man is beset on all sides by the iniquities of the
                                                        selfish and the tyranny of evil men. Blessed is he who, in the name of charity and
                                                        good will.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="conversation-new-message">
                                        <form>
                                            <div class="form-group">
                                                <textarea class="form-control" rows="2" placeholder="Enter your message..."></textarea>
                                            </div>
                                            <div class="clearfix">
                                                <button type="submit" class="btn btn-success pull-right">Send message</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="main-box clearfix">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#tab-users" data-toggle="tab">Users</a></li>
                                    <li><a href="#tab-products" data-toggle="tab">Products</a></li>
                                    <li><a href="#tab-todo" data-toggle="tab">Todo</a></li>
                                </ul>
                                <div class="tab-content tab-content-body clearfix">
                                    <div class="tab-pane fade in active" id="tab-users">
                                        <ul class="widget-users row">
                                            <li class="col-md-6">
                                                <div class="img">
                                                    <img src="/r_backend/img/samples/scarlet.png" alt=""/>
                                                </div>
                                                <div class="details">
                                                    <div class="name">
                                                        <a href="#">Scarlett Johansson</a>
                                                    </div>
                                                    <div class="time">
                                                        <i class="fa fa-clock-o"></i> Last online: 5 minutes ago
                                                    </div>
                                                    <div class="type">
                                                        <span class="label label-danger">Admin</span>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="col-md-6">
                                                <div class="img">
                                                    <img src="/r_backend/img/samples/kunis.png" alt=""/>
                                                </div>
                                                <div class="details">
                                                    <div class="name">
                                                        <a href="#">Mila Kunis</a>
                                                    </div>
                                                    <div class="time online">
                                                        <i class="fa fa-check-circle"></i> Online
                                                    </div>
                                                    <div class="type">
                                                        <span class="label label-warning">Pending</span>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="col-md-6">
                                                <div class="img">
                                                    <img src="/r_backend/img/samples/ryan.png" alt=""/>
                                                </div>
                                                <div class="details">
                                                    <div class="name">
                                                        <a href="#">Ryan Gossling</a>
                                                    </div>
                                                    <div class="time online">
                                                        <i class="fa fa-check-circle"></i> Online
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="col-md-6">
                                                <div class="img">
                                                    <img src="/r_backend/img/samples/robert.png" alt=""/>
                                                </div>
                                                <div class="details">
                                                    <div class="name">
                                                        <a href="#">Robert Downey Jr.</a>
                                                    </div>
                                                    <div class="time">
                                                        <i class="fa fa-clock-o"></i> Last online: Thursday
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="col-md-6">
                                                <div class="img">
                                                    <img src="/r_backend/img/samples/emma.png" alt=""/>
                                                </div>
                                                <div class="details">
                                                    <div class="name">
                                                        <a href="#">Emma Watson</a>
                                                    </div>
                                                    <div class="time">
                                                        <i class="fa fa-clock-o"></i> Last online: 1 week ago
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="col-md-6">
                                                <div class="img">
                                                    <img src="/r_backend/img/samples/george.png" alt=""/>
                                                </div>
                                                <div class="details">
                                                    <div class="name">
                                                        <a href="#">George Clooney</a>
                                                    </div>
                                                    <div class="time">
                                                        <i class="fa fa-clock-o"></i> Last online: 1 month ago
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="col-md-6">
                                                <div class="img">
                                                    <img src="/r_backend/img/samples/kunis.png" alt=""/>
                                                </div>
                                                <div class="details">
                                                    <div class="name">
                                                        <a href="#">Mila Kunis</a>
                                                    </div>
                                                    <div class="time online">
                                                        <i class="fa fa-check-circle"></i> Online
                                                    </div>
                                                    <div class="type">
                                                        <span class="label label-warning">Pending</span>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="col-md-6">
                                                <div class="img">
                                                    <img src="/r_backend/img/samples/ryan.png" alt=""/>
                                                </div>
                                                <div class="details">
                                                    <div class="name">
                                                        <a href="#">Ryan Gossling</a>
                                                    </div>
                                                    <div class="time online">
                                                        <i class="fa fa-check-circle"></i> Online
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                        <br/>
                                        <a href="#" class="btn btn-success pull-right">View all users</a>
                                    </div>
                                    <div class="tab-pane fade" id="tab-products">
                                        <ul class="widget-products">
                                            <li>
                                                <a href="#">
                                                            <span class="img">
                                                                <img src="/r_backend/img/samples/ipad.png" alt=""/>
                                                            </span>
                                                            <span class="product clearfix">
                                                                <span class="name">
                                                                    iPad mini 32GB WiFi Black&Slate
                                                                </span>
                                                                <span class="price">
                                                                    <i class="fa fa-money"></i> &dollar;320,00
                                                                </span>
                                                                <span class="warranty">
                                                                    <i class="fa fa-certificate"></i> Warranty: 2 years
                                                                </span>
                                                            </span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                            <span class="img">
                                                                <img src="/r_backend/img/samples/ipad.png" alt=""/>
                                                            </span>
                                                            <span class="product clearfix">
                                                                <span class="name">
                                                                    iPad mini 16GB WiFi Black&Slate
                                                                </span>
                                                                <span class="price">
                                                                    <i class="fa fa-money"></i> &dollar;273,68
                                                                </span>
                                                                <span class="warranty">
                                                                    <i class="fa fa-certificate"></i> Warranty: 2 years
                                                                </span>
                                                            </span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                            <span class="img">
                                                                <img src="/r_backend/img/samples/ipad.png" alt=""/>
                                                            </span>
                                                            <span class="product clearfix">
                                                                <span class="name">
                                                                iPad mini 32GB WiFi Cellular Black&Slate
                                                                </span>
                                                                <span class="price">
                                                                <i class="fa fa-money"></i> &dollar;447,29
                                                                </span>
                                                                <span class="warranty">
                                                                <i class="fa fa-certificate"></i> Warranty: 4 years
                                                                </span>
                                                            </span>
                                                </a>
                                            </li>
                                        </ul>
                                        <br/>
                                        <a href="#" class="btn btn-success pull-right">View all users</a>
                                    </div>
                                    <div class="tab-pane fade" id="tab-todo">
                                        <ul class="widget-todo">
                                            <li class="clearfix">
                                                <div class="name">
                                                    <div class="checkbox-nice">
                                                        <input type="checkbox" id="todo-1"/>
                                                        <label for="todo-1">
                                                            New products introduction <span class="label label-danger">High Priority</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="clearfix">
                                                <div class="name">
                                                    <div class="checkbox-nice">
                                                        <input type="checkbox" id="todo-2"/>
                                                        <label for="todo-2">
                                                            Checking the stock <span class="label label-success">Low Priority</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="actions">
                                                    <a href="#" class="table-link">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    <a href="#" class="table-link danger">
                                                        <i class="fa fa-trash-o"></i>
                                                    </a>
                                                </div>
                                            </li>
                                            <li class="clearfix">
                                                <div class="name">
                                                    <div class="checkbox-nice">
                                                        <input type="checkbox" id="todo-3" checked="checked"/>
                                                        <label for="todo-3">
                                                            Buying coffee <span class="label label-warning">Medium Priority</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="actions">
                                                    <a href="#" class="table-link">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    <a href="#" class="table-link danger">
                                                        <i class="fa fa-trash-o"></i>
                                                    </a>
                                                </div>
                                            </li>
                                            <li class="clearfix">
                                                <div class="name">
                                                    <div class="checkbox-nice">
                                                        <input type="checkbox" id="todo-4"/>
                                                        <label for="todo-4">
                                                            New marketing campaign <span class="label label-success">Low Priority</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="clearfix">
                                                <div class="name">
                                                    <div class="checkbox-nice">
                                                        <input type="checkbox" id="todo-5"/>
                                                        <label for="todo-5">
                                                            Calling Mom <span class="label label-warning">Medium Priority</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="actions">
                                                    <a href="#" class="table-link badge">
                                                        <i class="fa fa-cog"></i>
                                                    </a>
                                                </div>
                                            </li>
                                            <li class="clearfix">
                                                <div class="name">
                                                    <div class="checkbox-nice">
                                                        <input type="checkbox" id="todo-6"/>
                                                        <label for="todo-6">
                                                            Ryan's birthday <span class="label label-danger">High Priority</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="clearfix">
                                                <div class="name">
                                                    <div class="checkbox-nice">
                                                        <input type="checkbox" id="todo-7"/>
                                                        <label for="todo-7">
                                                            Printing new flyer <span class="label label-success">Low Priority</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="clearfix">
                                                <div class="name">
                                                    <div class="checkbox-nice">
                                                        <input type="checkbox" id="todo-8"/>
                                                        <label for="todo-8">
                                                            Mila and Ryan wedding <span class="label label-danger">High Priority</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="clearfix">
                                                <div class="name">
                                                    <div class="checkbox-nice">
                                                        <input type="checkbox" id="todo-9"/>
                                                        <label for="todo-9">
                                                            Checking the stock <span class="label label-success">Low Priority</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="main-box">
                            <div class="main-box-body clearfix">
                                <div id="calendar"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="main-box">
                            <header class="main-box-header clearfix">
                                <h2 class="pull-left">Visitors location</h2>
                                <div class="icon-box pull-right">
                                    <a href="#" class="btn pull-left">
                                        <i class="fa fa-refresh"></i>
                                    </a>
                                    <a href="#" class="btn pull-left">
                                        <i class="fa fa-cog"></i>
                                    </a>
                                </div>
                            </header>
                            <div class="main-box-body clearfix">
                                <div id="world-map" style="width: 100%; height: 400px"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer id="footer-bar" class="row">
            <p id="footer-copyright" class="col-xs-12">
                &copy; 2014 <a href="http://www.adbee.sk/" target="_blank">Adbee digital</a>. Powered by Centaurus Theme.
            </p>
        </footer>
    </div>
@stop

