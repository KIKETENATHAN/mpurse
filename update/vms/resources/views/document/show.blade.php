@extends('layouts.app')
@section('page-title')
    {{__('Document Details')}}
@endsection

@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{route('dashboard')}}"><h1>{{__('Dashboard')}}</h1></a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{route('document.index')}}">{{__('Document')}}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">{{__('Details')}}</a>
        </li>
    </ul>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="cdxemail-contain">
                @include('document.main')
                <div class="email-body">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{__('Basic Details')}}</h4>
                            <div class="float-right">
                                @if(Gate::check('edit document'))
                                    <a class="btn btn-primary" data-bs-toggle="tooltip"
                                       data-bs-original-title="{{__('Preview')}}"
                                       href="{{!empty($latestVersion->document)? asset(Storage::url('upload/document/')).'/'.$latestVersion->document : '#'}}"
                                       target="_blank"><i data-feather="maximize"> </i></a>
                                @endif
                                @if(Gate::check('download document'))
                                    <a class="btn btn-primary" data-bs-toggle="tooltip"
                                       data-bs-original-title="{{__('Download')}}"
                                       href="{{!empty($latestVersion->document)? asset(Storage::url('upload/document/')).'/'.$latestVersion->document : '#'}}"><i
                                            data-feather="download"> </i></a>
                                @endif
                                @if(Gate::check('preview document'))
                                    <a class="btn btn-primary customModal" data-bs-toggle="tooltip"
                                       data-bs-original-title="{{__('Edit')}}" href="#"
                                       data-url="{{ route('document.edit',$document->id) }}"
                                       data-title="{{__('Edit Support')}}"> <i data-feather="edit"></i></a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane fade active show" id="Primary">
                                    <ul class="mail-list" data-simplebar>
                                        <li>
                                            <div class="mail-item">
                                                <i class="like-mail text-warning" data-feather="star"></i>
                                                <h6 class="parson-name">{{__('Document Name')}}</h6>
                                                <p>{{$document->name}}</p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="mail-item">
                                                <i class="like-mail text-warning" data-feather="star"></i>
                                                <h6 class="parson-name">{{__('Category')}}</h6>
                                                <p>{{ !empty($document->category)?$document->category->title:'-' }}</p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="mail-item">
                                                <i class="like-mail text-warning" data-feather="star"></i>
                                                <h6 class="parson-name">{{__('Sub Category')}}</h6>
                                                <p>{{ !empty($document->subCategory)?$document->subCategory->title:'-' }}</p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="mail-item">
                                                <i class="like-mail text-warning" data-feather="star"></i>
                                                <h6 class="parson-name">{{__('Created By')}}</h6>
                                                <p>{{!empty($document->createdBy)?$document->createdBy->name:''}}</p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="mail-item">
                                                <i class="like-mail text-warning" data-feather="star"></i>
                                                <h6 class="parson-name">{{__('Created At')}}</h6>
                                                <p>{{dateFormat($document->created_at)}}</p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="mail-item">
                                                <i class="like-mail text-warning" data-feather="star"></i>
                                                <h6 class="parson-name">{{__('Tags')}}</h6>
                                                <p>
                                                    @foreach($document->tags() as $tag)
                                                        {{$tag->title}},
                                                    @endforeach
                                                </p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="mail-item">
                                                <i class="like-mail text-warning" data-feather="star"></i>
                                                <h6 class="parson-name">{{__('Description')}}</h6>
                                                <p>{{$document->description}} </p>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

