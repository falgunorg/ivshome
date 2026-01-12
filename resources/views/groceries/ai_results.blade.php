@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <h2 class="page-header">
                <i class="fa fa-magic"></i> Suggested Recipes
                <small class="pull-right">Based on your stock</small>
            </h2>
        </div>
    </div>

    <div class="row display-flex">
        @forelse($recipes as $recipe)
        <div class="col-xs-12 col-sm-6 col-md-4">
            <div class="box box-widget widget-user-2 recipe-card">
                <div class="widget-user-header bg-yellow">
                    <h3 class="widget-user-username" style="margin-left: 0; font-weight: 600;">
                        {{ $recipe['name'] }}
                    </h3>
                </div>
                <div class="box-footer">
                    <p><strong><i class="fa fa-book"></i> Instructions:</strong></p>
                    <div class="recipe-instructions">
                        {{ $recipe['instructions'] }}
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-xs-12 text-center">
            <div class="alert alert-info">No recipes found. Try adding more items to your grocery list!</div>
        </div>
        @endforelse
    </div>

    <div class="row" style="margin-top: 20px; margin-bottom: 30px;">
        <div class="col-xs-12">
            <a href="{{ route('groceries.index') }}" class="btn btn-default btn-flat">
                <i class="fa fa-arrow-left"></i> Back to Groceries
            </a>
        </div>
    </div>
</div>

<style>
    /* Ensure cards are uniform and responsive */
    .recipe-card {
        margin-bottom: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        border-radius: 4px;
        transition: transform 0.2s;
    }
    .recipe-card:hover {
        transform: translateY(-5px);
    }

    /* Fixed height for content area with scrolling if needed */
    .recipe-instructions {
        white-space: pre-wrap;      /* Maintains line breaks but wraps text */
        word-break: break-word;     /* Prevents long words from breaking layout */
        height: 250px;              /* Standardized height */
        overflow-y: auto;           /* Scrollable if text is long */
        padding-right: 5px;
        line-height: 1.6;
    }

    /* Custom Scrollbar for the instructions */
    .recipe-instructions::-webkit-scrollbar {
        width: 4px;
    }
    .recipe-instructions::-webkit-scrollbar-thumb {
        background: #f39c12;
        border-radius: 10px;
    }

    /* Helper for equal height cards on larger screens */
    @media (min-width: 768px) {
        .display-flex {
            display: flex;
            flex-wrap: wrap;
        }
        .display-flex > [class*='col-'] {
            display: flex;
            flex-direction: column;
        }
        .recipe-card {
            flex: 1;
        }
    }
</style>
@endsection