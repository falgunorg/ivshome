@extends('layouts.master')

@section('top')
<link rel="stylesheet" href="{{ asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
{{-- Trix Editor Styles --}}
<link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
<script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
@endsection {{-- This was missing --}}

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0" style="background: #fff; border-radius: 15px; overflow: hidden;">

                {{-- Header Image Section --}}
                <div class="position-relative">
                    @if($recipe->image)
                    <img src="{{ $recipe->show_photo }}" 
                         class="card-img-top" 
                         alt="{{ $recipe->title }}" 
                         style="height: 400px; width: 100%; object-fit: cover;">
                    @else
                    <div style="height: 300px; background: #eee; display: flex; align-items: center; justify-content: center;">
                        <span class="text-muted">No Image Available</span>
                    </div>
                    @endif

                    <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(transparent, rgba(0,0,0,0.8)); color: white;">
                        <h1 class="display-4 fw-bold mb-0" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
                            {{ $recipe->title }}
                        </h1>
                    </div>
                </div>

                <div class="card-body p-5" style="padding:15px">
                    <div class="row">
                        {{-- Main Content: Instructions --}}
                        <div class="col-lg-8">
                            <h3 class="mb-4 border-bottom pb-2" style="color: #333;">Instructions</h3>
                            <div class="recipe-instructions trix-content">
                                {{-- Render Trix HTML Content safely --}}
                                {!! $recipe->instructions !!}
                            </div>
                        </div>

                        {{-- Sidebar: Notes & Actions --}}
                        <div class="col-lg-4">
                            <div class="bg-light p-4 rounded shadow-sm border">
                                <h5 class="fw-bold mb-3"><i class="fa fa-sticky-note"></i> Notes</h5>
                                <p class="text-muted" style="font-style: italic; white-space: pre-line;">
                                    {{ $recipe->note ?: 'No extra notes provided.' }}
                                </p>
                                <hr>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('recipes.index') }}" class="btn btn-default btn-block">
                                        <i class="fa fa-arrow-left"></i> Back to List
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-muted text-center py-3 bg-white">
                    <small>Created on {{ $recipe->created_at->format('M d, Y') }} at {{ $recipe->created_at->format('H:i') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling for the Trix-rendered content */
    .recipe-instructions {
        line-height: 1.8;
        font-size: 1.1rem;
        color: #444;
    }
    .recipe-instructions ul, .recipe-instructions ol {
        margin-bottom: 1.5rem;
        padding-left: 2rem;
    }
    .recipe-instructions li {
        margin-bottom: 0.5rem;
    }
    /* Ensure images inside Trix don't break the layout */
    .trix-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
    }
</style>
@endsection