@extends('layout')
@section('title', 'New Tweet')
@section('header')
    @include('header')
@endsection
@section('content')
    <div class="container">
        <div class="form-group mt-2">
            <form id="postForm" method="post">
                <div class="form-group">
                    <div id="groupBtnUploadImage">
                        @if(Session::has('image_tmp'))
                            <img src="/media/tmp/{!! Session::get('image_tmp') !!}" width="180" class="img-thumbnail">
                            <button type="button" class="btn btn-sm btn-danger deleteImage" data-path="{!! Session::get('image_path') !!}">Delete</button>
                            <input type="hidden" value="/media/tmp/{!! Session::get('image_tmp') !!}" name="imageTmp">
                        @else
                            <div class="image-wapper mb5">
                                <div class="image-wapper-label">
                                    Add image
                                </div>
                                <div class="image-wapper-take">
                                    <div class="jfu-container" id="jfu-plugin-b22da094fc3c-45e7-f95f-6c1af9d2d458"><span class="jfu-btn-upload"><span><span style="position:relative; cursor:pointer"> <i class="fa fa-camera camera-add-image"></i><i class="fa fa-plus-circle plus-add-image"></i></span></span><input id="postMedia" name="postMedia[]" type="file" class="input-file jfu-input-file" accept="image/*" data-bind="uploader: UploadOptions"></span></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label for="exampleTextarea">Tweet content</label>
                    <textarea class="form-control" name="tweetContent" id="tweetContent" rows="3"></textarea>
                </div>
                <fieldset class="form-group">
                    <legend>Time option</legend>
                    <div class="form-group" style="position: relative">
                        <input type='text' class="form-control" id='datepicker' name="timeOption"/>
                    </div>
                </fieldset>
                <button type="submit" class="btn btn-primary" id="btnSend">Submit</button>
            </form>
        </div>
    </div>
@endsection
@section('css')
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-datetimepicker.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('gritter/jquery.gritter.css') }}">
    <style>
        .formTitle{font-size:18px;}
        .image-wapper{border:1px solid #ccc;background:#f9f9f9;position: relative;overflow: hidden;} .image-wapper .image-wapper-label{text-align:center;margin-top:20px;color:#1c60a7} .image-wapper .image-wapper-take{text-align:center;margin-top:10px} .image-wapper .image-wapper-take .jfu-btn-upload{width:100%;height:100%;background-color:#f9f9f9;border:none;cursor:pointer}.camera-add-image{color:#ebebeb;font-size:70px}.plus-add-image{position:absolute;top:-20px;left:27px;font-size:25px;color:#5bc3e9} .image-wapper .image-wapper-des{text-align:center;margin-top:10px;line-height:20px;color:#555}.jfu-input-file{position:absolute;top:0;right:0;margin:0;opacity:0;filter:alpha(opacity=0);font-size:23px;direction:ltr;cursor:pointer;min-width:100%;min-height:100%}
        .select2 {
            max-width:100%!important;
        }
        .error{display:block;color:red;}
        .groupForm{position:relative;}
        #preloader{position:fixed;top:0;left:0;width:100%;height:100%;background-color:#e4e7ea;z-index:10000;}
        #status{width:30px;height:30px;position:absolute;left:50%;top:50%;margin:-15px 0 0 -15px;font-size:32px;}
        #preloaderInBox{position:absolute;top:0;left:0;width:100%;height:100%;background-color:#e4e7ea;z-index:10000;opacity:0.8;}
        .note-group-select-from-files {
            display: none;
        }
    </style>

@endsection
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/moment.min.js" type="text/javascript"></script>
    <script src="{{ URL::asset('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ URL::asset('gritter/jquery.gritter.min.js') }}"></script>
    <script type="text/javascript">
        $(function () {
            $('#datepicker').datetimepicker({
                format: "Y/MM/DD H:m:s"
            });
        });
        $("#postMedia").on("change", function (e) {
            e.preventDefault();
            var files = $("#postMedia").prop("files");
            var totalFile=files.length;
            if(totalFile<=1){
                for(var i=0;i<totalFile;i++)
                {
                    var formData = new FormData();
                    formData.append("file", files[i]);
                    var xhrRequest = $.ajax({
                        url: "{!! route('uploadImage') !!}",
                        headers: {"X-CSRF-TOKEN": $("meta[name=_token]").attr("content")},
                        type: "post",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        dataType:"json",
                        success:function(result) {
                            if(result.success==true){
                                $('#groupBtnUploadImage').empty();
                                $('#groupBtnUploadImage').append('<img src="/media/tmp/'+result.file_name+'" width="180" class="img-thumbnail mr-2">');
                                $('#groupBtnUploadImage').append('<button type="button" class="btn btn-sm btn-danger deleteImage" data-path="'+result.image_path+'">Delete</button>');
                                $('#groupBtnUploadImage').append('<input type="hidden" name="imageTmp" value="/media/tmp/'+result.file_name+'">');
                                jQuery.gritter.add({
                                    title: "Notification!",
                                    text: result.message,
                                    class_name: "growl-success",
                                    sticky: false,
                                    time: ""
                                });
                            }else{
                                jQuery.gritter.add({
                                    title: "Notification!",
                                    text: result.message,
                                    class_name: "growl-danger",
                                    sticky: false,
                                    time: ""
                                });
                            }
                        }
                    });
                }
            }else{

            }
        });
        $("#postForm").on("click",".deleteImage",function() {
            var formData = new FormData();
            formData.append("image", $("input[name=imageTmp]").val());
            formData.append("dataPath", $(this).attr('data-path'));
            var xhrRequest = $.ajax({
                url: "{!! route('deleteImage') !!}",
                headers: {"X-CSRF-TOKEN": $("meta[name=_token]").attr("content")},
                type: "post",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                dataType:"json",
                success:function(result) {
                    if(result.success==true){
                        jQuery.gritter.add({
                            title: "Notification!",
                            text: result.message,
                            class_name: "growl-success",
                            sticky: false,
                            time: ""
                        });
                        location.reload();
                    }
                }
            });
            return false;
        });
        $("#postForm").on("click","#btnSend",function() {
            var formData = new FormData();
            formData.append("content", $("textarea[name=tweetContent]").val());
            formData.append("image", $("input[name=imageTmp]").val());
            formData.append("timeOption", $("input[name=timeOption]").val());
            var xhrRequest = $.ajax({
                url: "{!! route('tweet.request') !!}",
                headers: {"X-CSRF-TOKEN": $("meta[name=_token]").attr("content")},
                type: "post",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                dataType:"json",
                success:function(result) {
                    console.log(result);
                    if(result.success==true){
                        jQuery.gritter.add({
                            title: "Notification!",
                            text: result.message,
                            class_name: "growl-success",
                            sticky: false,
                            time: ""
                        });
                        location.reload();
                    }else{
                        jQuery.gritter.add({
                            title: "Notification!",
                            text: result.message,
                            class_name: "growl-danger",
                            sticky: false,
                            time: ""
                        });
                    }
                }
            });
            return false;
        });
    </script>
@endsection