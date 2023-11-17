window.dropblockeditor = (config) => {
    return {
        iframe: null,

        dropList: null,

        insert: false,

        device: 'desktop',

        lastTopPos: 0,

        cursorPos: 0,

        currentDragItem: null,

        insertBeforeClasses: ['after:opacity-100', 'after:top-0', 'after:h-[5px]'],

        insertAfterClasses: ['after:opacity-100', 'after:bottom-0', 'after:h-[5px]'],

        init() {
            this.iframe = document.getElementById("frame");

            this.dropList = document.querySelector("[drop-list]");

            document.addEventListener('keydown', (e) => this.undo(e, this));
            document.addEventListener('keydown', (e) => this.redo(e, this));

            this.initListeners()

            this.iframe.addEventListener("load", () => {
                this.initListeners()

                this.iframe.contentWindow.scrollTo(0, this.lastTopPos)
            })
        },

        initListeners() {
            let root = this.iframe.contentWindow.document;

            root.addEventListener('keydown', (e) => this.undo(e, this));
            root.addEventListener('keydown', (e) => this.redo(e, this));

            this.dropList.querySelectorAll('[drag-item]').forEach(el => {
                el.addEventListener("dragstart", e => {
                    e.target.setAttribute('inserting', true)
                })

                el.addEventListener('dragend', e => {
                    e.target.removeAttribute('inserting')
                })

                el.addEventListener('dragover', e => e.preventDefault())
            })

            root.querySelectorAll('[drop-placeholder]').forEach(el => {
                el.addEventListener('dragover', e => e.preventDefault())

                el.addEventListener('dragenter', e => {
                    e.preventDefault()

                    e.target.classList.add('bg-gray-200/50', 'animate-pulse');
                })

                el.addEventListener('dragleave', e => {
                    e.preventDefault()

                    e.target.classList.remove('bg-gray-200/50', 'animate-pulse');
                })

                el.addEventListener('drop', e => {
                    e.preventDefault

                    if (!e.target.closest('[drop-placeholder]')) {
                        return;
                    }

                    let insertingEl = document.querySelector('[inserting]')

                    if (insertingEl != null) {
                        this.component().call('insertBlock', insertingEl.dataset.block, 0)

                        insertingEl.removeAttribute('inserting')

                        insertingEl = false;

                        return
                    }
                })
            })

            root.querySelectorAll('[drag-item]').forEach(el => {
                el.addEventListener('click', e => {
                    Livewire.dispatch('blockEditComponentSelected', {
                        blockId: e.target.closest('[drag-item]').dataset.block
                    });
                }, false)

                el.addEventListener('dragstart', e => {
                    e.target.setAttribute('dragging', true)
                    this.currentDragItem = el
                })

                el.addEventListener('dragover', e => {
                    e.preventDefault()

                    let dragitem = e.target.closest('[drag-item]')

                    if (this.currentDragItem === dragitem) {
                        return;
                    }

                    let placement = this.beforeOrAfterEl(e, dragitem)
                    let isPreviousSibling = this.currentDragItem != null ? this.currentDragItem.previousElementSibling : false;
                    let isNextSibling = this.currentDragItem != null ? this.currentDragItem.nextElementSibling : false;

                    if (dragitem != isNextSibling && placement === 'before') {
                        dragitem.classList.remove(...this.insertAfterClasses);
                        dragitem.classList.add(...this.insertBeforeClasses);

                    } else if (dragitem != isPreviousSibling && placement === 'after') {
                        dragitem.classList.remove(...this.insertBeforeClasses);
                        dragitem.classList.add(...this.insertAfterClasses);

                    } else {
                        dragitem.classList.remove(...this.insertBeforeClasses, ...this.insertAfterClasses);
                    }
                })

                el.addEventListener('dragend', e => {
                    e.target.removeAttribute('dragging')
                    this.currentDragItem = null
                })

                el.addEventListener('dragenter', e => {
                    if (e.target.hasAttribute('drag-item')) {
                        e.target.setAttribute('is-target', true)
                    }
                })

                el.addEventListener('dragleave', e => {
                    e.preventDefault

                    if (e.target.hasAttribute('is-target')) {
                        e.target.classList.remove(...this.insertAfterClasses, ...this.insertBeforeClasses);
                    }
                })

                el.addEventListener('drop', e => {
                    e.preventDefault

                    let draggingEl = root.querySelector('[dragging]')
                    let insertingEl = document.querySelector('[inserting]')

                    if (!e.target.closest('[drag-item]')) {
                        return;
                    }

                    if (e.target.hasAttribute('drag-item')) {
                        e.target.classList.remove(...this.insertAfterClasses, ...this.insertBeforeClasses);
                    }

                    this.lastTopPos = root.documentElement.scrollTop

                    let placement = this.beforeOrAfterEl(e, e.target.closest('[drag-item]'))

                    if (insertingEl != null) {
                        this.component().call('insertBlock', insertingEl.dataset.block, e.target.closest('[drag-item]').dataset.block, placement)

                        insertingEl.removeAttribute('inserting')

                        insertingEl = false;

                        return
                    }

                    if (placement === 'after') {
                        e.target.closest('[drag-item]').after(draggingEl)
                    } else {
                        e.target.closest('[drag-item]').before(draggingEl)
                    }

                    let orderIds = Array.from(root.querySelectorAll('[drag-item]'))
                        .map(itemEl => itemEl.dataset.block)

                    this.component().call('reorder', orderIds)
                })
            })
        },

        isBefore(container, target, current) {
            let targetFound = false;
            let currentFound = false;
            let before = false;

            container.querySelectorAll('[drag-item]').forEach(el => {
                if (before) {
                    return;
                }

                targetFound = targetFound ? true : el == target;
                currentFound = currentFound ? true : el == current;

                if (targetFound === false && currentFound === true) {
                    before = true;
                    return;
                }
            })

            return before;
        },

        beforeOrAfterEl(e, el) {
            let bounding = el.getBoundingClientRect()

            let upperHalfStart = bounding.y;
            let upperHalfEnd = upperHalfStart + (bounding.height / 2);

            let bottomHalfStart = upperHalfEnd;
            let bottomHalfEnd = bottomHalfStart + (bounding.height / 2)

            let isTopHalf = e.clientY >= upperHalfStart && e.clientY <= upperHalfEnd
            let isBottomHalf = e.clientY >= bottomHalfStart && e.clientY <= bottomHalfEnd

            if (isTopHalf) {
                return 'before'
            } else if (isBottomHalf) {
                return 'after'
            }

            return false
        },

        component() {
            return Livewire.find(
                frame.closest('[wire\\:id]').getAttribute('wire:id')
            );
        },

        undo(e, editor) {
            if ((e.ctrlKey || e.metaKey) && ! e.shiftKey && e.key === 'z') {
                e.preventDefault();

                if (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1) {
                    window.history.forward();
                }

                editor.component().call('undo')
            }
        },

        redo(e, editor) {
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'z') {
                e.preventDefault();

                if (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1) {
                    window.history.forward();
                }

                editor.component().call('redo')
            }
        },
    }
}
