<div class="mt-1 md:mt-0 md:border md:rounded-md mx-1 bg-white z-50
            md:focus-within:shadow-xl md:focus-within:scale-150 md:focus-within:translate-x-20"
    x-data="{
        search: '{{ $search ?? '' }}',
        type: 1,
        keyword: '{{ $search ?? '' }}',
        tags: {{ json_encode($tagsSearch ?? []) }},
        tagsSelect: [],
        tagsSelectLoading: false,
        to: null,
        focus: false,
    
        formChange(submit = false) {
            if (this.type == 1) return this.keyword = this.search
    
            clearTimeout(this.to)
            this.to = setTimeout(() => {
                this.getTags()
            }, 500)
    
            if (this.tags.length < 3 && submit) this.addTag(this.search)
        },
    
        get showKeywordButton() { return this.type == 2 },
        get showTagButton() { return this.type == 1 },
        get inputSize() { return this.search.length < 6 ? 6 : this.search.length },
    
        focusForm() { document.getElementById('textform').focus() },
    
        changeType() {
            if (this.type == 2) {
                this.type = 1
                this.search = this.keyword;
                return this.focusForm()
            }
            this.type = 2
            this.search = ''
            this.focusForm()
        },
    
        addTag(tag = '') {
            const newTag = this.tags.find(t => t == tag.trim()) ? false : tag.trim()
            if (newTag) this.tags = [newTag, ...this.tags].sort()
            this.search = ''
            this.tagsSelect = []
        },
    
        async getTags() {
            this.tagsSelectLoading = true;
            try {
                const response = await fetch('/post/tags/' + this.search)
                const data = await response.json()
                this.tagsSelect = data
                this.tagsSelectLoading = false;
            } catch (error) {
                this.tagsSelectLoading = false;
                console.log(error)
            }
        },
    
        removeTag(tag = '') { this.tags = this.tags.filter(t => t != tag.trim()) },
    }">
    <form id="searchform" action="/">
        <div class="md:flex items-center">
            <div class="md:flex items-center">
                <input type="hidden" name="search" x-model="keyword">
                <template x-for="tag in tags">
                    <input type="hidden" name="tags[]" :value="tag">
                </template>

                {{-- keyword search --}}
                <button type="button" x-on:click="changeType" class="ml-1 px-2 font-semibold border rounded"
                    x-show="showKeywordButton">keyword :
                    <span x-text="keyword"></span></button>

                {{-- tags Search --}}
                <span class="mx-1 font-semibold">Tags : (<span x-text="tags.length"></span>/3)
                </span>
                <button type="button" x-show="showTagButton" x-on:click="changeType"
                    class="px-1 font-extrabold rounded border mr-1" x-show="tags.length < 6">+</button>
                <template x-for="tag in tags">
                    <div class="mr-1 px-1 border rounded">
                        <span x-text="tag"></span>
                        <button type="button" class="px-1" x-on:click="removeTag(tag)">x</button>
                    </div>
                </template>

                {{-- input search --}}
                <input id="textform" :size="inputSize" :maxlength="type == 2 ? 10 : 25"
                    @keydown.enter.prevent="formChange(true)" x-on:input="formChange(false)" x-on:focus="focus = true"
                    type="search" x-model="search" :placeholder="type == 1 ? 'keyword' : 'tag'"
                    class="p-1 px-2 w-full md:w-auto focus:outline-none">

                {{-- select tags --}}
                <div x-show="tagsSelectLoading"
                    class="mx-1 w-4 h-4 border-b-2 border-gray-900 rounded-full animate-spin"></div>
                <template x-for="tag in tagsSelect">
                    <button class="px-1 mr-1 mb-1 border rounded" x-text="tag+' + '" x-on:click="addTag(tag)"></button>
                </template>

            </div>
            <button onclick="document.getElementById('searchform').submit()"
                class="px-1 md:p-1 md:px-2 border md:rounded-r-md w-full md:w-auto h-full">🔍</button>
        </div>
    </form>
</div>
