@extends('layouts.layout3')
@section('title', __("Version History"))
@section('page_title_css', 'pad-no')

@section('page_title')
@include('includes.nfy.dark_title', ["dark_title"=> 'Version History']) 
@endsection

@section('content')
<section class="content">
<div class="panel-body box-body permission">
    <div class="col-md-12">
    @foreach($versionArray as $index => $version)
        <article class="post tag" id="versions">
            <span class="expand"><lord-icon src="{{ url('/js/lord-icon/iltqorsz.json') }}" trigger="loop" colors="primary:#000000,secondary:#000000" stroke="100" style="width:25px;height:25px"></lord-icon></span>
            <span class="collapse"><lord-icon src="{{ url('/js/lord-icon/rivoakkk.json') }}" trigger="loop" colors="primary:#000000,secondary:#000000" stroke="100" style="width:25px;height:25px"></lord-icon></span>

            <h4 class="title">{{$version->title}}</h4>
            <div class="target_versions clearfix">
                <div class="col-md-10">
                    <div class="row">
                    <div id="generalAccordian{{$index}}" class="panel-collapse collapse {{ $index == 0 ? 'in' : '' }} " {{ $index == 0 ? "aria-expanded=true" : "" }}>
                        @foreach($version->events as $t) 
                            <li>{{$t}}</li>
                        @endforeach
                    </div>
                    </div>
                </div>
            </div>
        </article>
        @endforeach
    </div>
</div>


</section>
@endsection

@push('head')
@include('includes.csslib')
<link href="{!! CommonHelper::asset('newcss/select2.min.css') !!}" rel="stylesheet" />
<script type="text/javascript" src="{!! CommonHelper::asset('js/lord-icon/lord-icon-2.0.2.js') !!}"></script>
<style>
    .panel-heading{
        background-color: #313131;
        color:white;
    }
    #roles {
        width: 100%;
        overflow:hidden;
        margin-top: 10px;
    }
    span.expand,span.collapse {
        float: right;
        background-color:transparent;
        border: none;
        color: white;
        cursor: pointer;
        top: 7px;
        position: relative;
    }
    i.fa.fa-plus,i.fa.fa-minus {
        font-size: 13px;
        padding: 3px;
        color:#CD3333;
    }
    .target_versions {
        display:none;
    }
    .collapse {
        display:none;
    }
    .panel-collapse {
        margin-left: 21px;
    }
</style>
@endpush

@push('lib')
@include('includes.jslib')
<script src="{!! CommonHelper::asset('newjs/select2.min.js') !!}"></script>
<script type="text/javascript">
        $(document).ready(function () {

            $('#versions .expand').click(function() {
                $(this).parent().find('.target_versions').show(500);
                $(this).parent().find('.expand').hide(0);
                $(this).parent().find('.collapse').show(0);
		    });

            $('#versions .collapse').click(function() {
                $(this).parent().find('.target_versions').hide(500);
                $(this).parent().find('.expand').show(0);
                $(this).parent().find('.collapse').hide(0);
            });

        });
    </script>
@endpush