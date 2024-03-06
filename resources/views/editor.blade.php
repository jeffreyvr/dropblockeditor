<div>
    @isset($jsPath)
        <script>{!! file_get_contents($jsPath) !!}</script>
    @endisset
    @isset($cssPath)
        <style>{!! file_get_contents($cssPath) !!}</style>
    @endisset

    <div
        x-cloak
        x-data="dropblockeditor()"
        class="dropblockeditor flex flex-col min-h-screen bg-gray-100">
        <div class="{{ config('dropblockeditor.brand.colors.topbar_bg', 'bg-white') }} px-5 py-5 border-b text-white flex justify-between flex-initial">
            <div class="flex items-center">
                @if($logo = config('dropblockeditor.brand.logo', false))
                    <div class="mr-2">{!! $logo !!}</div>
                @endif
                <div>
                    {{ $title ?? __('No title') }}
                </div>
            </div>
            <div class="flex items-center gap-2">
                <div class="flex gap-2 mx-4">
                    <button wire:click="undo" @disabled(!$this->canUndo()) class="{{ $this->canUndo() ? '' : 'opacity-25' }}" aria-label="Undo change">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15m0 0l6.75 6.75M4.5 12l6.75-6.75" />
                        </svg>
                    </button>
                    <button wire:click="redo" @disabled(!$this->canRedo()) class="{{ $this->canRedo() ? '' : 'opacity-25' }}" aria-label="Redo change">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
                        </svg>
                    </button>
                </div>
                @foreach($buttons as $i => $button)
                    @livewire($button, ['properties' => $this->updateProperties()], key('button-' . $i))
                @endforeach
            </div>
        </div>

        <div class="flex flex-initial h-full grow">

            <div class="relative flex-1 flex justify-center">
                <iframe id="frame" srcdoc="{{ $result }}" class="h-full" :class="device === 'mobile' ? 'w-[320px]' : device === 'tablet' ? 'w-[768px]' : 'w-full'"></iframe>
                <div class="absolute right-4 top-4 flex items-center bg-white rounded-md border shadow-sm">
                    <button x-on:click="device = 'mobile'" class="p-2 border-r" :class="device === 'mobile' ? 'text-gray-800' : 'text-gray-300'">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                        </svg>
                    </button>

                    <button x-on:click="device = 'tablet'" class="p-2 border-r" :class="device === 'tablet' ? 'text-gray-800' : 'text-gray-300'">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5h3m-6.75 2.25h10.5a2.25 2.25 0 002.25-2.25v-15a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 4.5v15a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </button>

                    <button x-on:click="device = 'desktop'" class="p-2" :class="device === 'desktop' ? 'text-gray-800' : 'text-gray-300'">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                        </svg>
                    </button>
                </div>
                <div wire:loading class="absolute right-5 bottom-5">
                    <svg class="animate-spin h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>

            <aside class="w-[400px] shrink-0 shadow-lg relative bg-white">
                <div
                    drop-list
                    x-cloak
                    x-show="! $wire.activeBlockIndex"
                    class="flex flex-col pb-4">
                    @php
                        $blockGroups = collect($blocks)->map(function($block, $i) {
                            return [
                                'original_index' => $i,
                                'block' => $this->getBlockFromClassName($block['class']),
                            ];
                        })->groupBy(function($item) {
                            return $item['block']->getCategory();
                        })->sortBy(function($item, $key) {
                            return $key;
                        })->toArray();
                    @endphp

                    @foreach($blockGroups as $category => $categoryBlocks)
                        <div class="px-4 pt-4">
                            @if($category)
                            <h2 class="mb-2 font-medium">{{ $category }}</h2>
                            @endif
                            <div class="grid grid-cols-3 gap-4">
                                @foreach($categoryBlocks as $groupedBlock)
                                    @php
                                        $i = $groupedBlock['original_index'];
                                        $block = $groupedBlock['block'];
                                    @endphp

                                    <div drag-item draggable="true" data-block="{{ $i }}" class="shadow-sm mb-2 text-center bg-white border border-gray-100 rounded-lg px-3 py-2 flex flex-col justify-center items-center cursor-grab active:cursor-grabbing hover:border-gray-200">
                                        @if($block->getIcon())
                                            <div class="opacity-50 mb-1">{!! $block->getIcon() !!}</div>
                                        @endif

                                        <span class="text-sm">{{ $block->getTitle() }}</span>

                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="opacity-25 w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM18.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                                        </svg>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($activeBlock)
                <div class="border-b mb-4">
                    <div class="border-b bg-white flex justify-between items-center">
                        <div class="flex items-center">
                            <button wire:click="$set('activeBlockIndex', false)" class="p-4 text-gray-500 hover:text-gray-800 border-r">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                                </svg>
                            </button>
                            <div class="p-4">
                                <h2 class="font-medium flex items-center">
                                    {{ $activeBlock->title }}
                                </h2>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <button wire:click="cloneBlock" aria-label="Clone" class="p-4 text-gray-500 hover:text-gray-800 border-l">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                                </svg>
                            </button>
                            <button wire:click="deleteBlock" aria-label="Delete" class="p-4 text-gray-500 hover:text-gray-800 border-l">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="p-4">
                        @if(!empty($activeBlock->blockEditComponent))
                            <div class="mb-4">
                                @livewire($activeBlock->blockEditComponent, [
                                    'position' => $activeBlockIndex,
                                    'block' => $activeBlock->toArray(),
                                ], key($this->prepareActiveBlockKey($activeBlockIndex)))
                            </div>
                        @else
                            {{ __('This block is not editable.') }}
                        @endif
                    </div>
                </div>
                @endif
            </aside>
        </div>
    </div>
</div>
