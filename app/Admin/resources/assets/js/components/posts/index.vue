<template>
    <ol class="breadcrumb">
        <li><a href="/admin">Home</a></li>
        <li class="active">Posts</li>
    </ol>

    <div class="create-button">
        <a href="/admin/posts/create" class="btn btn-success"><i class="fa fa-pencil"></i> Write New</a>
    </div>

    <div class="filters">
        <div class="row">
            <div class="col-md-6">
                <input type="text" v-model="titleFilter" @keyup="changeFilter" placeholder="Filter by title..." class="form-control">
            </div>
            <div class="col-md-6">
                <select v-model="categoryFilter" @change="changeFilter" id="category" class="form-control">
                    <option :value="null" selected>Filter by category...</option>
                    <option v-for="category in categories" :value="category.name">
                        {{ category.name }}
                    </option>
                </select>
            </div>
        </div>
    </div>

    <table class="Posts table table-striped table-hover">
        <thead>
            <tr>
                <th v-if="categoryFilter"><a href="#" @click.prevent="sortBy('order')">Order <i :class="orderIcon('order')"></i></a></th>
                <th><a href="#" @click.prevent="sortBy('title')">Title <i :class="orderIcon('title')"></i></a></th>
                <th><a href="#" @click.prevent="sortBy('category.name')">Category <i :class="orderIcon('category.name')"></i></a></th>
                <th><a href="#" @click.prevent="sortBy('created_at')">Created <i :class="orderIcon('created_at')"></i></a></th>
                <th><a href="#" @click.prevent="sortBy('is_published')">Published <i :class="orderIcon('is_published')"></i></a></th>
                <th class="text-centered"><a>Showing</a></th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="post in posts | filterBy titleFilter in 'title' | filterBy categoryFilter in 'category.name' | orderBy sortKey sortDir"
                class="Post" :class="{ 'Post--unpublished': !post.is_published }">

                <td v-if="categoryFilter" class="order">
                    <input class="post__order"
                           type="number"
                           value="{{ post.order }}"
                           @keyup.enter="blur"
                           @focusout="reorder(post, $event)"
                           number>
                </td>

                <td>
                    <a href="/admin/posts/{{ post.id }}" @click.prevent="goToPost(post)">{{ post.title }}</a>
                    <span v-if="post.is_locked" data-toggle="tooltip" data-placement="top"
                          title="Locked by {{ userName(post.locked_by_id) }}"><i class="fa fa-lock"></i></span>
                </td>

                <td>{{ post.category ? post.category.name : '' }}</td>

                <td>{{ formatTimestamp(post.created_at) }}</td>

                <td class="published">
                    <div class="switch">
                        <input class="cmn-toggle cmn-toggle-round-sm"
                               id="is_published_{{post.id}}"
                               type="checkbox"
                               name="is_published"
                               v-model="post.is_published"
                               @change="post.publish(post.is_published)">
                        <label for="is_published_{{post.id}}"></label>
                    </div>
                </td>

                <td class="text-centered">
                    <span v-if="post.is_showing" class="showing"><i class="fa fa-check"></i></span>
                    <span v-else class="not-showing"><i class="fa fa-times"></i></span>
                </td>

            </tr>
        </tbody>
    </table>

</template>

<script>
    import moment from 'moment';
    import swal from 'sweetalert';
    import Post from '../../models/post';
    import Category from '../../models/category';
    import User from '../../models/user';

    export default {

        props: ['current-user'],

        data: function () {
            return {
                posts: [],
                categories: [],
                users: [],
                titleFilter: null,
                categoryFilter: null,
                sortKey: 'order',
                sortDir: 1
            }
        },

        created: function() {
            this.fetchPosts();
            this.fetchCategories();
            this.fetchUsers();
        },

        ready: function () {
            this.$nextTick(function() {
                this.initTooltips();
            }.bind(this));
        },

        methods: {

            fetchPosts: function() {
                this.$http.get('posts?include=category&orderBy=updated_at|desc&count=1000').then(function (response) {
                    this.$set('posts', response.data.data.map(function (post) {
                        return new Post(post);
                    }));
                });
            },

            fetchCategories: function () {
                this.$http.get('categories').then(function (response) {
                    this.$set('categories', response.data.data.map(function (category) {
                        return new Category(category);
                    }));
                });
            },

            fetchUsers: function () {
                this.$http.get('users').then(function (response) {
                    this.$set('users', response.data.data.map(function (user) {
                        return new User(user);
                    }));
                });
            },

            goToPost: function (post) {
                if (! post.is_locked) {
                    window.location = '/admin/posts/'+post.id;
                } else {
                    swal({
                        html: true,
                        title: "Are you sure?",
                        text: "The post is currently opened by <strong>"+this.userName(post.locked_by_id)+"</strong>. "+
                            "If you proceed and they are still editing the post, you may overwrite each other's work.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, unlock it!",
                        cancelButtonText: "Nevermind"
                    }, function () {
                        window.location = '/admin/posts/'+post.id;
                    }.bind(this));
                }
            },

            userName: function (userId) {
                if (! userId || ! this.users || !this.users.length) {
                    return '';
                }

                var user = this.users.filter(function (user) {
                    return user.id == userId;
                })[0];

                return user.name;
            },

            formatTimestamp: function (timestamp) {
                return moment.unix(timestamp).format('MMM D, YYYY');
            },

            sortBy: function (key) {
                if (this.sortKey == key) {
                    this.sortDir = this.sortDir * -1;
                } else {
                    this.sortKey = key;
                    this.sortDir = 1;
                }
            },

            orderIcon: function (key) {
                if (key == this.sortKey) {
                    return this.sortDir > 0 ? 'fa fa-sort-asc' : 'fa fa-sort-desc'
                }

                return 'fa fa-unsorted';
            },

            initTooltips: function () {
                this.$nextTick(function() {
                    $('[data-toggle="tooltip"]').tooltip();
                });
            },

            changeFilter: function () {
                this.initTooltips();
            }

        }

    }
</script>