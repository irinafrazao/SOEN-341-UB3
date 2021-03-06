<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title> Jux - Details of a Question </title>

    <!-- Bootstrap core CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/css/blog-post.css" rel="stylesheet">

    <!-- general background css -->
    <link href="/css/general.css" rel="stylesheet">

    <!--Styles for icons-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!--Token needed for ajax call when editing user info-->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .click {
            background: none !important;
            border: none;
            padding: 0 !important;
            font: inherit;
            cursor: pointer;
        }
    </style>

</head>

<body class="Site">

<!-- Navigation -->
@include('header')

<!-- Page Content -->
<div class="container Site-content">

    <div class="row">

        <!-- Post Content Column -->
        <div class="col-lg-8">

            <!-- Title -->
            <h1 class="mt-4">{{$question->title}}</h1>

            <!-- Author -->
            <p class="lead">
                by
                {{$user[0]->name}}
            </p>

            <hr>

            <!-- Date/Time -->
            Posted on the <span style="text-decoration: underline;">
                            <?php $parts = explode('-', $question->created_at);
                $month = (DateTime::createFromFormat('!m', $parts[1]))->format('F');
                echo substr($parts[2], 0, 2) . "th of $month of $parts[0] at " . substr($parts[2], 2)?> </span>


            <!-- Question content -->
            <hr>
            <table class="table" style="background-color: #FAFAFA;">
                <tbody>
                <tr class="question">
                    <td width=70%>
                        {{$question->content}} </br></br>

                        <!--Display labels of question-->
                        @if(($question->labels) != "")
                            <?php $parts = explode(',', $question->labels)?>
                            @foreach($parts as $label)
                                <span id="labels-styled" style="margin-bottom: 10px;">{{$label}}</span>
                            @endforeach
                        @endif
                    </td>
                </tr>
                </tbody>
            </table>


            <!-- Reply Form -->
            <div class="card my-4">
                <h5 class="card-header">Leave a Reply:</h5>
                <div class="card-body">
                    <form method="POST" action="/question/reply/{{$question->id}}">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <textarea name="body" class="form-control" rows="3"></textarea>
                        </div>

                        <div>
                            <div>
                               @guest
                                  <a href="{{ route('login') }}" name="submit" class="btn btn-primary">Submit</a>
                               @else
                                  <button type="submit" class="btn btn-primary">Submit</button>
                               @endguest
                            </div>
                        </div>

                    </form>
                </div>
            </div>

            <!-- Details of the replies -->
            <p>
                {{count($replies)}} answer(s)
            </p>
            @if(count($replies) > 0)
                @foreach($replies as $reply)
                    <table class="answers table" width=100% style="background-color: #FAFAFA;">
                        <br>
                        <tr class="answer">
                            <td class="answer-text" width=70%>
                                <p> {{$reply->content}}</p>
                            </td>

                            <!-- -A reply can be liked or disliked.-->
                            <td class="rating" style="vertical-align:middle; " width=15%>
                                <div style="color:teal; float:left; margin: 0 40% 0 50%;">
                                    <a style="color: <?php if (null !== Auth::user()) {
                                        $flag = FALSE;
                                        foreach ($likes as $like) {
                                            if ($like->user_id == Auth::user()->id && $like->reply_id == $reply->id) {
                                                echo 'rgb(176,224,230)';
                                                $flag = TRUE;
                                            }
                                        }
                                        if ($flag != TRUE) echo 'rgb(30, 144, 255)';
                                    } else echo 'rgb(30, 144, 255)'; ?>"
                                       class="like click" id="{{$reply->id}}bl" dusk="like-{{$reply->id}}" name="like">
                                        <i class="fa fa-thumbs-up" aria-hidden="true"></i>
                                    </a>
                                    <span dusk="numlike-{{$reply->id}}" id="{{$reply->id}}l">{{$reply->likectr}}</span>
                                </div>
                                <br/>

                                <div style="color:teal; float:left; margin: 0 40% 0 50%;">
                                    <a style="color: <?php if (null !== Auth::user()) {
                                        $flag = FALSE;
                                        foreach ($dislikes as $like) {
                                            if ($like->user_id == Auth::user()->id && $like->reply_id == $reply->id) {
                                                echo 'rgb(176,224,230)';
                                                $flag = TRUE;
                                            }
                                        }
                                        if ($flag != TRUE) echo 'rgb(30, 144, 255)';
                                    } else echo 'rgb(30, 144, 255)'; ?>"
                                       class="dislike click" id="{{$reply->id}}bdl" dusk="dislike-{{$reply->id}}" name="dislike">
                                        <i class="fa fa-thumbs-up" style="transform: rotate(180deg); "
                                           aria-hidden="true"></i>
                                    </a>
                                    <span dusk="numdislike-{{$reply->id}}" id="{{$reply->id}}dl">{{$reply->dislikectr}}</span>
                                </div>
                            </td>

                            <!-- -A reply can be accepted, rejected or normalized by owner.-->
                            <td class="w3-padding w3-xlarge w3-text-green" style="vertical-align:middle;" width=15%>
                                @if($qOwner)
                                    <a dusk="accept-owner-{{$reply->id}}" class="accept click"  name="accept" id="accept">
                                        <i class="fa fa-check-circle <?php if ($reply->status == 1) echo 'fa-2x'; ?>"
                                           style="color:rgb(45, 188, 16)" id="{{$reply->id}}a"></i>
                                    </a>
                                    <a class="normalize click" dusk="normal-owner-{{$reply->id}}" name="normal" id="normal">
                                        <i class="fa fa-bars <?php if ($reply->status == 0) echo 'fa-2x'; ?>"
                                           style="color:rgb(106, 115, 124)" id="{{$reply->id}}n"></i>
                                    </a>
                                    <a class="reject click" dusk="reject-owner-{{$reply->id}}" name="reject" id="reject">
                                        <i class="fa fa-ban <?php if ($reply->status == -1) echo 'fa-2x'; ?>"
                                           style="color:rgb(255, 0, 0)" id="{{$reply->id}}r"></i>
                                    </a>
                                @elseif($reply->status == -1)
                                    <i dusk="reject-user-{{$reply->id}}" class="fa fa-ban <?php echo 'fa-2x'; ?>" style="color:rgb(255, 0, 0)"></i>
                                @elseif($reply->status == 1)
                                    <i dusk="accept-user-{{$reply->id}}" class="fa fa-check-circle <?php echo 'fa-2x'; ?>"
                                       style="color:rgb(45, 188, 16)"></i>
                                @endif
                            </td>
                        </tr>

                        <!-- reply metadata -->
                        <tr>
                            <td style="border-top-style: none;">
                                <font size="2" color="grey">
                                    Replied on the
                                    <span style="text-decoration: underline;">
                                        <?php $parts = explode('-', $reply->created_at);
                                        $month = (DateTime::createFromFormat('!m', $parts[1]))->format('F');
                                        echo substr($parts[2], 0, 2) . "th of $month of $parts[0] at " . substr($parts[2], 2)?> </span>
                                    by
                                    <span style="text-decoration: underline;">
                                        <?php
                                        foreach ($name_data as $value) {
                                            if ($value->id == $reply->user_id) {
                                                echo $value->name;
                                            }
                                        }?></span>
                                </font>
                            </td>
                        <tr>
                    </table>
                @endforeach
            @else
                <!-- This will show if no replies have been made for a question. -->
                <p>No comments</p>
                @endif
                </tbody>
                <hr>
        </div>

        <!-- Sidebar Widgets Column -->
        <div class="col-md-4">
            @include('sidebar_search')
        </div>

    </div>
    <!-- /.row -->

</div>
<!-- /.container -->

<!-- Footer -->
@include('footer')

<!-- Bootstrap core JavaScript -->
<script src="/js/jquery.min.js"></script>
<script src="/js/bootstrap.bundle.min.js"></script>

<!--  script used for like and dislike replies! -->
<script type="text/javascript" src="{{ URL::asset('js/like-dislike.js') }}"></script>

<!-- script used for the accept/reject/normalize buttons! -->
<script type="text/javascript" src="{{ URL::asset('js/accpt-norm-rejct.js') }}"></script>

<!-- Script with function to list questions with labels -->
<script type="text/javascript" src="{{ URL::asset('js/filter-labels.js') }}"></script>

</body>

</html>
