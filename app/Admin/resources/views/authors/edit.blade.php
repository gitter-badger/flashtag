@extends('admin::layout')

@section('content')
    <ol class="breadcrumb">
        <li><a href="/admin">Home</a></li>
        <li><a href="/admin/authors">Authors</a></li>
        <li class="active">{{ $author->name }}</li>
    </ol>

    <form class="Category EditForm" action="{{ route('admin.authors.update', [$author->id]) }}" method="POST">
        {{ csrf_field() }}
        {{ method_field('PUT') }}

        <section class="info row">
            <div class="col-md-6 col-md-offset-6 clearfix">
                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                    <a href="#delete" class="btn btn-danger"><i class="fa fa-trash"></i> Delete</a>
                    <a href="/admin/authors" class="btn btn-default"><i class="fa fa-close"></i> Close</a>
                </div>
            </div>
        </section>

        @include('admin::authors.form')

    </form>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script>
        $(document).ready(function(){
            // CKEditor
            $('.rich-editor').each(function(){
                CKEDITOR.replace($(this).attr('id'));
            });

            // Select2
            $('.select').select2();

            // Delete Button
            $('#delete').click(function(e){
                e.preventDefault();
                del();
            });
        });

        var del = function() {
            var self = this;
            swal({
                title: 'Are you sure?',
                text: 'You will not be able to recover this author!',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, delete it!',
                closeOnConfirm: false,
                showLoaderOnConfirm: true
            }, function () {
                $.ajax({
                    url: '/api/authors/{{ $author->id }}',
                    method: 'DELETE',
                    headers: { Authorization: localStorage.getItem('jwt-token') }
                }).done(function(){
                    self.deleted = true;
                    window.location = '/admin/authors';
                });
            });
        };

        var notify = function (type, message) {
            if (type == 'success') {
                var icon = "fa fa-thumbs-o-up";
            } else if (type == 'warning') {
                var icon = "fa fa-warning";
            }
            $.notify({
                icon: icon,
                message: message
            }, {
                type: type,
                delay: 3000,
                offset: { x: 20, y: 70 }
            });
        };
    </script>
@endsection
