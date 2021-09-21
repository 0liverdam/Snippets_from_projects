@extends('components.app')

@section('content')
    <head>
        <!-- Styles -->
        <link href="{{ asset('css/reset.css') }}" rel="stylesheet">
        <link href="{{ asset('css/style-guide.css') }}" rel="stylesheet">
        <link href="{{ asset('css/inspiration.css') }}" rel="stylesheet">
        <link href="{{ asset('css/content-search-page.css')}}" rel="stylesheet">
        <script src="/js/vimeoPlayer.js"></script>
    </head>

    <div id="mobile-title">
        <div class="mobile-title-holder">
            <div class="page-max-width-title mx-auto">
                <h4 class="page-title-name page_title">{{__('header.inspiration')}}</h4>
                <img id="title-search-icon" src="/storage/search/search-icon.svg"
                    alt="Search" onclick="openSearch()">
            </div>
        </div>
        <div id="search-page">
            <div class="search-header">
                <form class="search-field" name="search" method="POST" action="/contentSearchForm">
                    @csrf
                    <input type="image" class="search-icon" src="/storage/search/search-icon.svg" alt="Submit"/>
                    <div class="vertical-line"></div>
                    <input class="search-field-input" name="search" type="textfield"
                           placeholder="{{__('content-search.search')}}" autocomplete="off" required>
                </form>
                <img class="search-close" src="/storage/search/close.svg" alt="Close" onclick="closeMobileSearch()">
            </div>
            <div class="autocomplete-container">

            </div>
        </div>
    </div>
    <div id="page_wrapper">
        <div id="desktop-title">
            <div class="page_title-holder">
                <div class="page-max-width-title mx-auto">
                    <h4 class="page-title-name page_title">{{__('header.inspiration')}}</h4>
                </div>
            </div>
            <div class="page_search-bar">
                <div class="page-max-width-title">
                    <div class="desktop-search-header">
                        <form class="search-field" name="search" method="POST" action="/contentSearchForm">
                            @csrf
                            <input type="image" class="search-icon" src="/storage/search/search-icon.svg" alt="Submit">
                            <div class="vertical-line"></div>
                            <input class="search-field-input" name="search" type="textfield"
                                placeholder="{{__('content-search.search')}}" autocomplete="off" required>
                        </form>
                        <img class="search-close" src="/storage/search/close.svg" alt="Close" onclick="closeDesktopSearch()">
                    </div>
                    <div class="autocomplete-container">

                    </div>
                </div>
            </div>
        </div>

        <!-- introvideo -->
        @if($intro != null)
            <div class="card page_card">
                <div class="card-title page_card-title-holder">
                    <h4 class="page_card-title">{{__('inspiration.Intro-video')}}</h4>
                </div>
                <div class="content-player" id="content" @auth c_id="{{$intro->id}}"@endauth>
                    <div id="player" data-plyr-provider="vimeo" data-plyr-embed-id="{{$intro->content_id}}"></div>
                </div>
            </div>
        @endif

        @auth
            <div class="card page_card">
                <div class="card-title page_card-title-holder">
                    <h4 class="page_card-title m-0 p-0">
                        {{ __('inspiration.rcm_title') }}
                    </h4>
                    <h5 class="page_card-title m-0 p-0">
                        {{ __('inspiration.rcm_body') }}
                    </h5>
                </div>
                <div class="card-body m-0 p-0">
                    <div id="rcm_container">
                        <div id="rcm_content-holder" class="mx-auto">
                            <a href="{{ route('recommendations') }}">
                                <img id="block3" class="img1-background" src="{{asset('storage/'.$recommended->thumbnail)}}"
                                    alt="background"
                                />
                                <div id="rcm_strip">
                                    <img id="block2" src="{{'storage/recommendation/strip.svg'}}" alt="strip"
                                        style="width: 100%"/>
                                    <div id="rcm_strip-content-holder">
                                        <div class="row justify-content-around">
                                            <div class="col px-0 ml-3 rcm_strip-text">
                                                <div>
                                                    <p class="text-nowrap m-0 p-0">{{ __('inspiration.rcm_strip') }}</p>
                                                </div>
                                            </div>
                                            <div class="col px-0">
                                                <div class="ml-auto">
                                                    <img id="rcm_img" src="storage/recommendation/squares.svg"
                                                        alt="Responsive image"/>
                                                </div>
                                            </div>
                                            <div class="col px-0 rcm_strip-text m-auto">
                                                <p class="rcm_strip-text text-nowrap m-0 p-0">{{$recommended->contentCount}}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endauth

        <div class="card page_card">
            <div class="card-title page_card-title-holder">
                <h4 class="page_card-title">
                    {{ __('inspiration.ctg_title') }}
                </h4>
                <h5 class="page_card-title">
                    {{ __('inspiration.ctg_body') }}
                </h5>
            </div>
            <div class="ctg_card-body">
                @foreach($categories as $key=>$ctg)
                    <div class="ctg_col">
                        <a href="category/{{$ctg->slug}}">
                            <div class="ctg_img-holder ctg_{{$ctg->slug}}">
                                <a href="category/{{$ctg->slug}}">
                                    <img src="{{asset('/storage/categories/'.$ctg->slug.'-icon.svg')}}"
                                            alt="{{$ctg->name}}" class="ctg_img"/>
                                </a>
                            </div>
                        </a>
                        <div class="ctg_name-holder">
                            <a href="category/{{$ctg->slug}}" class="ctg_links">
                                <p class="ctg_name mx-auto">{{ __('inspiration.' . $ctg->slug)}}</p>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card page_card cmp_card">
            <div class="card-title page_card-title-holder">
                <h4 class="page_card-title cmp_card-title">
                    {{__('inspiration.cmp_courses')}}
                </h4>
            </div>
            <div class="">
                @foreach ($categories as $cat)
                    <p class="cmp_category-title cmp_{{$cat->slug}}">{{__('inspiration.' . $cat->slug)}}</p>
                    <div class="cmp_competences">
                        @foreach($cat->competences as $comp)
                            <div class="two-blocks-videos">
                                <a href="/competence/{{$comp->slug}}" class="cmp_links">
                                    <div class="cmp_thumbnail-holder">
                                        <img class="cmp_thumbnail"
                                            src="{{asset('/storage/'.$comp->thumbnail)}}"
                                            alt="{{$comp->name}}">
                                    </div>
                                    <div class="">
                                        <div class="cmp_title-holder">
                                            <p class="cmp_title">
                                                {{ __('inspiration.' . $comp->slug)}}
                                            </p>
                                            <p class="cmp_material-count">
                                                {{$comp->total}} {{ trans_choice('inspiration.materials', $comp->total) }}
                                            </p>
                                        </div>
                                        <div class="cmp_content-type-holder">
                                            <div id="cmp_video" class="cmp_content-type-card">
                                                <img src="/storage/competences/video.svg" alt="videos" class="cmp_content-type-image"/>
                                                <p class="ctg_name mx-auto">
                                                    {{$comp->videos_count}}
                                                </p>
                                            </div>
                                            <div id="cmp_podcast" class="cmp_content-type-card">
                                                <img src="/storage/competences/podcast.svg" alt="podcasts" class="cmp_content-type-image"/>
                                                <p class="ctg_name mx-autot">
                                                    {{$comp->podcasts_count}}
                                                </p>
                                            </div>
                                            <div id="cmp_article" class="cmp_content-type-card">
                                                <img src="/storage/competences/post.svg" alt="articles" class="cmp_content-type-image"/>
                                                <p class="ctg_name mx-auto">
                                                    {{$comp->articles_count}}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <img class="cmp_right-arrow"
                                        src="{{asset('/storage/competences/arrow.svg')}}"/>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        //The Plyr player
        const player = new Plyr('#player', {captions: {active: true}});

        @auth
            //set viewed this these functions should be in their own js file that is importet
            function set_viewed() {
                let c_id = $('#content').attr('c_id');
                let data = 'content_id=' + c_id;
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{{asset('content/viewed')}}',
                    type: 'post',
                    data: data,
                    success: function (res) {
                    },
                    error: function (xhr, status, error) {
                        const errorMessage = xhr.status + ': ' + xhr.statusText;
                        alert('Error - ' + errorMessage)
                    }
                });
            }

            $('.content-player, .readButton').one('click', function () {
                set_viewed();
            });
        @endauth

        /*this is a kinda hacked solution for the animation as i move the search icon on the inspiration header and hide it,
        simultanously making the one on the search window visible and doing the same backwards when closing the search page
        i did this because both images have 2 different functions, it might be possible to change the function dynamically*/
        /* this animation does not work on the desktop version and will need a revision */
        function openSearch() {
            document.getElementsByClassName("search-icon")[0].style.visibility = "hidden";
            $("#title-search-icon").animate(
                {
                    right: ($(window).width() - $("#title-search-icon").width() - 21) + "px",
                    width: "20px",
                    top: "+=3px"
                }, {
                    complete: function () {
                        document.getElementById("title-search-icon").style.visibility = "hidden";
                        document.getElementsByClassName("search-icon")[0].style.visibility = "visible";
                    }
                });

            $('.autocomplete-container').load('getRecentSearches');
            $("#search-page").fadeIn();
            document.getElementsByClassName("search-field-input")[0].focus();
        }

        function closeMobileSearch() {
            document.getElementById("title-search-icon").style.visibility = "visible";
            document.getElementsByClassName("search-icon")[0].style.visibility = "hidden";
            $("#title-search-icon").animate({
                right: "16px",
                width: "25px",
                top: "-=3px"
            });

            $("#search-page").fadeOut();
            $('.autocomplete-container').empty();

        }

        function closeDesktopSearch() {
            $('.search-field-input').val("");
            $('.autocomplete-container').empty();
        }

        $(document).on('click', function (e) {
            $('.autocomplete-container').empty();
        });

        function fillForm(search) {
            $('.search-field-input').val(search);
            document.forms["search"].submit();
        }


        $('.search-field-input').on('keyup focus', function(e) {
            var search = e.target.value;
            if (search.length > 2) {
                $.get('contentSearchAutocomplete/' + search, function(data) {
                    $('.autocomplete-container').empty();
                    $('.autocomplete-container').html(data);
                });
            } else if (search.length <= 2) {
                $.get('getRecentSearches', function(data) {
                    $('.autocomplete-container').empty();
                    $('.autocomplete-container').html(data);
                });
            }
        });
    </script>
@endsection

