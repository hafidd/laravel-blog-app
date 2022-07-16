@extends('layout') @section('content')
    <div class="py-5 flex w-full">
        <!-- kiri -->
        <div class="px-1 md:px-4 lg:border-r-2 w-full">
            <h2 class="text-3xl font-bold mb-1">
                {{ $action === 'create' ? 'Create new post' : 'Update post' }}
            </h2>
            <form id="form" action="{{ $action === 'create' ? '/post/store' : '/post/' . $post->id . '/update' }}"
                method="post" enctype="multipart/form-data">
                @csrf
                <div class="text-gray-600">
                    <div class="">
                        <input type="text" name="title" value="{{ old('title') ?? ($post->title ?? '') }}"
                            maxlength="50" class="w-[30em] p-4 text-2xl font-semibold focus:outline-none"
                            placeholder="Post title...">
                        @error('title')
                            <p class="text-red-300 p-4">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="">
                        <input type="text" name="subtitle" value="{{ old('subtitle') ?? ($post->subtitle ?? '') }}"
                            maxlength="200" class="w-full p-4 text-xl font-semibold focus:outline-none "
                            placeholder="subtitle...">
                        @error('subtitle')
                            <p class="text-red-300 p-4">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- TAGS -->
                    <div class="pl-4 flex flex-wrap items-center">
                        {{-- <label for="tags" class="mr-2 text-xl font-semibold text-gray-500">Tags </label> --}}
                        <div class=""
                            x-data='{ 
                            newTag: "",
                            tagsSelect: [],
                            tagsSelectLoading: false,
                            to: null,
                            tags: {{ old('tags') ? json_encode(old('tags')) : (isset($tags) && !empty($tags) ? json_encode($tags) : '[]') }},
                            addTag() {
                                if(this.tags.length > 4 || this.newTag.replace(/[$-/:-?{-~!"^_`\[\]]/g, "").trim() === "") return;
                                this.tags=[
                                    this.newTag.replace(/[$-/:-?{-~!"^_`\[\]]/g, "").trim(), 
                                    ...this.tags.filter(t=>t!==this.newTag.replace(/[$-/:-?{-~!"^_`\[\]]/g, "").trim())
                                ].sort(); 
                                this.newTag= ""
                            },
                            getTags() {
                                clearTimeout(this.to)
                                this.to = setTimeout(async () => {
                                    this.tagsSelectLoading = true;
                                    try {
                                        const response = await fetch("/post/tags/" + this.newTag)
                                        const data = await response.json()
                                        this.tagsSelect = data
                                        this.tagsSelectLoading = false;
                                    } catch (error) {
                                        this.tagsSelectLoading = false;
                                        console.log(error)
                                    }
                                }, 500)
                            },
                            }'>
                            <div class="flex items-center">
                                <input x-model="newTag" :size="newTag.length + 1" type="text" placeholder="tags..."
                                    @keydown.enter.prevent='addTag' x-on:keyup="getTags"
                                    class="py-2 text-xl font-semibold focus:outline-none w-fit">
                                <div x-show="tagsSelectLoading"
                                    class="mx-1 w-4 h-4 border-b-2 border-gray-900 rounded-full animate-spin"></div>
                                <template x-for="tag in tagsSelect">
                                    <button type="button" class="px-1 border rounded mr-1"
                                        x-on:click="newTag=tag;addTag();tagsSelect=[]" x-text="tag"></button>
                                </template>
                            </div>
                            <template x-for="tag in tags">
                                <input type="hidden" name="tags[]" x-bind:value="tag">
                            </template>
                            <div x-bind:class="`flex items-center`">
                                <template x-for="tag in tags">
                                    <div class="px-2 h-fit border rounded-md ml-2">
                                        <span x-text="tag" class="text-lg font-semibold py-1"></span>
                                        <button type="button" x-on:click="tags=tags.filter(t=>t!==tag)">x</button>
                                    </div>
                                </template>
                            </div>
                        </div>
                        @error('tags')
                            <p class="text-red-300 p-4 w-full -ml-4">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="p-4">
                        <input type="file" name="picture" class="block w-fit cursor-pointer focus:outline-none">
                        @error('picture')
                            <p class="text-red-300 p-4">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4 px-4">
                        <textarea name="content" rows="10" placeholder="" class="p-4 text-lg w-full">
                            {{ old('content') ?? ($post->content ?? '') }}
                        </textarea>
                        @error('content')
                            <p class="text-red-300 p-4">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="text-right px-2">
                    <button class="px-6 py-2 border rounded-lg text-lg font-semibold mr-2">Save</button>
                    <button type="button"
                        onclick="
                        document.getElementById('form').setAttribute('action', (document.getElementById('form').getAttribute('action') + '?next=true'));
                        document.getElementById('form').submit();
                    "
                        class="px-6 py-2 border rounded-lg text-lg font-semibold">Next >></button>
                </div>
            </form>
        </div>

        <!-- Kanan -->
        <!-- <div class="hidden xl:block w-72 p-2 h-max shrink-0">
                                                                                                                                                                                                                                                                                                                                                                                            </div> -->
    </div>
    {{-- <script src="{{ asset('ckeditor/ckeditor.js') }}"></script> --}}
    <script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <script type="text/javascript">
        CKEDITOR.replace('content', {
            filebrowserUploadUrl: "{{ route('ckupload', ['_token' => csrf_token()]) }}",
            filebrowserUploadMethod: 'form'
        });
    </script>
@endsection
