<script setup>
    import { computed, ref, onMounted, watch, inject, nextTick, onUnmounted } from 'vue';
    import { useI18n } from "vue-i18n";

    import Pusher from 'pusher-js';
    import SparkMD5 from 'spark-md5';

    import WebApp from '@twa-dev/sdk';
    import { List, Checkbox } from '@erfanmola/televue';

    import { Vue3Lottie } from 'vue3-lottie';
    import { replaceColor } from 'lottie-colorify';
    import AnimationTasks from "../assets/lotties/animation-tasks.json";

    const i18nLocale = useI18n({ useScope: 'global' });
    
    // Set i18n locale based on the user's locale provided by <LocaleProvider>
    i18nLocale.locale.value = localStorage.getItem('simplist_locale') || inject('locale', 'en');

    // Define a reactive empty object that holds tasks
    const tasks = ref({});

    // Define number of visible tasks in list, values more than this will be folded
    const visibletasksCount = 5;
    // Define a reactive boolean value to control whether the task list is folded or expanded
    const showAllTasks = ref(false);
    
    // Define a reactive boolean value to toggle the visibility of add task input box
    const addingTask = ref(false);
    // Define a reactive null value that will hold our ref to <input> element of add task input box
    const addingTaskTitle = ref(null);
    // Define a reactive string value that will hold our v-model to add task input box
    const addingTaskTitleText = ref("");

    /* */

    // === Computed Values ===

    // Compute & Sort the tasks
    const tasksList = computed(() => {

        // This method will check whether we should return all of the tasks
        // Or we should return the first N task, based on state of `showAllTasks`

        // We sort the based on their `created_at` value in descending order

        return showAllTasks.value ?
               Object.fromEntries(Object.entries(tasks.value).sort((a, b) => { return b[1].created_at - a[1].created_at; })) : // return all tasks
               Object.fromEntries(Object.entries(tasks.value).sort((a, b) => { return b[1].created_at - a[1].created_at; }).slice(0, visibletasksCount)); // return task 0 to `visibletasksCount`

    });

    // Manipulate the colors of Lottie animated JSON file `AnimationTasks`
    // To match with Telegram Client Theme
    const AnimationTasksColored = computed(() => {

        let animation = AnimationTasks;

        animation = replaceColor([213,213,226], hexAlphaToRGB(
            getComputedStyle(document.body).getPropertyValue('--tg-theme-button-color'),
            0.625,
            getComputedStyle(document.body).getPropertyValue('--tg-theme-secondary-bg-color'),
        ), animation);

        animation = replaceColor([41,121,255], getComputedStyle(document.body).getPropertyValue('--tg-theme-button-color'), animation);

        animation = replaceColor([105,161,255], hexAlphaToRGB(
            getComputedStyle(document.body).getPropertyValue('--tg-theme-button-color'),
            0.625,
            getComputedStyle(document.body).getPropertyValue('--tg-theme-secondary-bg-color'),
        ), animation);

        animation = replaceColor([190,190,206], getComputedStyle(document.body).getPropertyValue('--tg-theme-button-color'), animation);

        animation = replaceColor([233,233,244], getComputedStyle(document.body).getPropertyValue('--tg-theme-bg-color'), animation);

        animation = replaceColor([181,209,255], hexAlphaToRGB(
            getComputedStyle(document.body).getPropertyValue('--tg-theme-button-color'),
            0.325,
            getComputedStyle(document.body).getPropertyValue('--tg-theme-secondary-bg-color'),
        ), animation);

        return animation;

    });

    // === Computed Values ===

    /* */

    // === UI Events ===

    const btnClickAddTask = async () => {

        // If we are not `addingTask`
        if (!(addingTask.value)) {

            // Then we are `addingTask` (:
            addingTask.value = true;

            // So we wait for the nextTick() of Vue to process the update on `addingTask`
            await nextTick();
            // Then we focus on the addingTask <input> element and show the keyboard
            addingTaskTitle.value.focus();

            // Note: the reason for `await nextTick();` is due to a bug in iOS Safari that does not show keyboard otherwise
        }

    };

    const btnClickDeleteTask = (taskID) => {

        WebApp.showPopup({
            title: i18nLocale.t('home.dialog_delete_task.title'),
            message: i18nLocale.t('home.dialog_delete_task.message'),
            buttons: [
                { id: "cancel",  type: "cancel"     , text: i18nLocale.t('home.dialog_delete_task.buttons.cancel') },
                { id: "confirm", type: "destructive", text: i18nLocale.t('home.dialog_delete_task.buttons.delete') },
            ]
        }, (id) => {

            if (id === 'confirm') {

                deleteTask(taskID);

            }

        });

    };

    const taskSwipeHandler = (direction, e) => {

        // If we are in RTL direction language
        if (document.body.classList.contains('rtl')) {

            if (direction === 'right' && !(e.currentTarget.classList.contains('swiped'))) {

                e.currentTarget.classList.add('swiped');

            }else if (direction === 'left' && e.currentTarget.classList.contains('swiped')) {

                e.currentTarget.classList.remove('swiped');

            }

        }else{

            if (direction === 'left' && !(e.currentTarget.classList.contains('swiped'))) {

                e.currentTarget.classList.add('swiped');

            }else if (direction === 'right' && e.currentTarget.classList.contains('swiped')) {

                e.currentTarget.classList.remove('swiped');

            }

        }

    };

    const taskKeyupHandler = async (e) => {

        await nextTick();

        // Hide the keyboard
        e.target.blur();

    };

    const taskUpdateHandler = async (e) => {

        // Update the value
        tasks.value[e.target.getAttribute('data-task-id')].title = e.target.value;

    };

    // === UI Events ===

    /* */

    // === Actions ===

    const addTask = async (e) => {

        // If addingTaskTitle <input> value is not empty
        if (addingTaskTitleText.value.length > 0) {
            
            // Generate a random MD5 hash and prefix it with `task_` as our taskID
            let taskID = `task_${ generateRandomMD5() }`;

            let task = {
                title: addingTaskTitleText.value,
                content: null, // null for now, may add details in future
                created_at: parseInt(Date.now() / 1000),
                updated_at: parseInt(Date.now() / 1000),
                done: false,
            };

            if (JSON.stringify(task).length > 4096) {
                return;
            }

            // Save the task in LocalStorage
            localStorage.setItem(taskID, JSON.stringify(task));
            // Add the task to `tasks` and reactivity magic happens and updates the UI
            tasks.value[taskID] = task;

            // Reset the addingTaskTitle <input> value
            addingTaskTitleText.value = "";
            // Hide the addingTaskTitle <input>
            addingTask.value = false;

            WebApp.CloudStorage.setItem(taskID, localStorage.getItem(taskID), (error, success) => {
                    
                if (error) {
                    
                    // In case of failure, we add the task to our `pending_tasks` list
                    let pending_tasks = JSON.parse(localStorage.getItem('pending_tasks')) || {};
                    pending_tasks[taskID] = task;
                    localStorage.setItem('pending_tasks', JSON.stringify(pending_tasks));
                    
                    WebApp.HapticFeedback.notificationOccurred('error');
                    return;

                }

                // Broadcast to our other active sessions (if any) that something has changed
                channel.trigger('client-fetch', JSON.stringify({ action: 'add' }));

                WebApp.HapticFeedback.notificationOccurred('success');

            });

        }else{

            // Hide the addingTaskTitle <input>
            addingTask.value = false;

        }

    };

    const fetchTasks = (useLocalStorage = true) => {

        // If useLocalStorage is true, we will populate the `tasks` variable with LocalStorage cached values until we reach server
        if (useLocalStorage) {

            let localTasks = Object.fromEntries(Object.keys(localStorage).filter((key) => {

                return /^task_[a-f0-9]{32}$/gi.test(key);

            }).map((key) => {

                return [ key, JSON.parse(localStorage.getItem(key)) ];

            }));

            tasks.value = localTasks;

        }

        WebApp.CloudStorage.getKeys((error, keys) => {
            
            if (error) {

                WebApp.HapticFeedback.notificationOccurred('error');
                return;

            }

            WebApp.CloudStorage.getItems(keys, (error, items) => {

                if (error) {

                    WebApp.HapticFeedback.notificationOccurred('error');
                    return;

                }

                WebApp.HapticFeedback.notificationOccurred('success');

                // Remove all the tasks from LocalStorage
                Object.keys(tasks.value).forEach((key) => {
                    
                    localStorage.removeItem(key);

                });

                // Set all the retrieved tasks in LocalStorage
                items = Object.fromEntries(
                    Object.entries(items).map(([ key, value ]) => { 
                        localStorage.setItem(key, value);
                        return [ key, JSON.parse(value) ]; 
                    })
                );

                // Update `tasks` value and let reactivity handle the UI update
                tasks.value = items;

            });

        });

    };

    const updateTask = async (taskID) => {

        // update the `update_at` timestamp
        tasks.value[taskID].updated_at = parseInt(Date.now() / 1000);

        if (JSON.stringify(tasks.value[taskID]).length > 4096) {
            return;
        }

        localStorage.setItem(taskID, JSON.stringify(tasks.value[taskID]));

        WebApp.CloudStorage.setItem(taskID, JSON.stringify(tasks.value[taskID]), (error, success) => {

            if (error) {

                // In case of failure, we add the task to our `pending_tasks` list
                let pending_tasks = JSON.parse(localStorage.getItem('pending_tasks')) || {};
                pending_tasks[taskID] = tasks.value[taskID];
                localStorage.setItem('pending_tasks', JSON.stringify(pending_tasks));
                
                WebApp.HapticFeedback.notificationOccurred('error');
                return;

            }

            // If the task is in `pending_tasks` list, delete it, we have just synced it!
            let pending_tasks = JSON.parse(localStorage.getItem('pending_tasks')) || {};
            delete pending_tasks[taskID];
            localStorage.setItem('pending_tasks', JSON.stringify(pending_tasks));
            
            // Broadcast to our other active sessions (if any) that something has changed
            channel.trigger('client-fetch', JSON.stringify({ action: 'toggle' }));

            WebApp.HapticFeedback.notificationOccurred('success');

        });

    };

    const deleteTask = async (taskID) => {

        delete tasks.value[taskID];

        localStorage.removeItem(taskID);

        WebApp.CloudStorage.removeItem(taskID, (error, success) => {

            if (error) {
                
                WebApp.HapticFeedback.notificationOccurred('error');
                return;

            }

            // If the task is in `pending_tasks` list, delete it, we have just deleted it!
            let pending_tasks = JSON.parse(localStorage.getItem('pending_tasks')) || {};
            delete pending_tasks[taskID];
            localStorage.setItem('pending_tasks', JSON.stringify(pending_tasks));

            // Broadcast to our other active sessions (if any) that something has changed
            channel.trigger('client-fetch', JSON.stringify({ action: 'delete' }));

            WebApp.HapticFeedback.notificationOccurred('success');

        });

    };

    const performPendingTasks = () => {

        // Retrieve the list of `pending_tasks`
        let pending_tasks = JSON.parse(localStorage.getItem('pending_tasks')) || {};

        Object.keys(pending_tasks).forEach((taskID) => {

            // Try updating them to CloudStorage
            WebApp.CloudStorage.setItem(taskID, JSON.stringify(pending_tasks[taskID]), (error, success) => {

                if (error) {
                    
                    return;

                }

                // Delete the task from `pending_tasks`, we have just synced it!
                let pending_tasks = JSON.parse(localStorage.getItem('pending_tasks')) || {};
                delete pending_tasks[taskID];
                localStorage.setItem('pending_tasks', JSON.stringify(pending_tasks));

            });

        });

    };

    // === Actions ===

    /* */

    // === Misc Methods ===

    const generateRandomMD5 = () => {
    
        // Generate a random string
        const randomString = Math.random().toString(36).substring(2, 10) + Math.random().toString(36).substring(2, 10);

        // Calculate MD5 hash of the random string using spark-md5 library
        return SparkMD5.hash(randomString);

    }

    // Convert Hex Color to an RGB color with consideration of an Alpha value and a backgorund color
    const hexAlphaToRGB = (colorHex, alpha, backgroundColorHex) => {

        const hexToRgb = (hex) => {
            hex = hex.replace(/^#/, '');
            const bigint = parseInt(hex, 16);
            const red = (bigint >> 16) & 255;
            const green = (bigint >> 8) & 255;
            const blue = bigint & 255;
            return [red, green, blue];
        };

        const rgbaColor = [...hexToRgb(colorHex), Math.round(alpha * 255)];
        const backgroundRgb = hexToRgb(backgroundColorHex);

        const opacity = rgbaColor[3] / 255;
        const blendedRed = Math.round((1 - opacity) * backgroundRgb[0] + opacity * rgbaColor[0]);
        const blendedGreen = Math.round((1 - opacity) * backgroundRgb[1] + opacity * rgbaColor[1]);
        const blendedBlue = Math.round((1 - opacity) * backgroundRgb[2] + opacity * rgbaColor[2]);

        return [blendedRed, blendedGreen, blendedBlue];

    };

    // === Misc Methods ===

    /* */

    onMounted(async () => {

        // Fetch tasks
        fetchTasks();

        // Watch for changes on `tasks`
        watch(tasks, () => {

            Object.entries(tasks.value).forEach(([ key, value ]) => {

                // If an item is changed
                if (JSON.stringify(value) !== (localStorage.getItem(key))) {

                    // Update it
                    updateTask(key);

                }

            });

        }, { deep: true });

        // When subscription to our private channel succeeds
        channel.bind('pusher:subscription_succeeded', () => {
            
            // We listen for an event called `client-fetch`
            channel.bind('client-fetch', function(data) {

                // Fetch tasks, but exclude the LocalStorage since something new has happened on the other clients
                fetchTasks(false);

            });

        });

        // We also try performing `pending_tasks` if there is any
        performPendingTasks();

    });

    onUnmounted(() => {

        channel.unbind_all();
        channel.unsubscribe(`private-${ WebApp.initDataUnsafe.user?.id }`);

    });

    // Initiate a Pusher instance with given keys in Environment Variables
    const pusher = new Pusher(import.meta.env.VITE_PUSHER_KEY, {
        cluster: 'ap2',
        authEndpoint: `${ import.meta.env.VITE_BACKEND_ENDPOINT }/pusher/auth`,
        auth: {
            params: {
                'initDataUnsafe': JSON.stringify(WebApp.initDataUnsafe),
            }
        }
    });

    // Subscribe to a private channel that is for our userId
    const channel = pusher.subscribe(`private-${ WebApp.initDataUnsafe.user?.id }`);

    WebApp.setHeaderColor('secondary_bg_color');
    WebApp.setBackgroundColor('secondary_bg_color');
    WebApp.BackButton.hide();
</script>

<template>
    <div id="container-home">

        <ul id="header-bar">

            <li>
                <router-link to="/settings">
                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" x="0" y="0" viewBox="0 0 32 32" style="fill: var(--tg-theme-button-color);">
                        <g>
                            <path d="M28.068 12h-.128a.934.934 0 0 1-.864-.6.924.924 0 0 1 .2-1.01l.091-.091a2.938 2.938 0 0 0 0-4.147l-1.511-1.51a2.935 2.935 0 0 0-4.146 0l-.091.091A.956.956 0 0 1 20 4.061v-.129A2.935 2.935 0 0 0 17.068 1h-2.136A2.935 2.935 0 0 0 12 3.932v.129a.956.956 0 0 1-1.614.668l-.086-.091a2.935 2.935 0 0 0-4.146 0l-1.516 1.51a2.938 2.938 0 0 0 0 4.147l.091.091a.935.935 0 0 1 .185 1.035.924.924 0 0 1-.854.579h-.128A2.935 2.935 0 0 0 1 14.932v2.136A2.935 2.935 0 0 0 3.932 20h.128a.934.934 0 0 1 .864.6.924.924 0 0 1-.2 1.01l-.091.091a2.938 2.938 0 0 0 0 4.147l1.51 1.509a2.934 2.934 0 0 0 4.147 0l.091-.091a.936.936 0 0 1 1.035-.185.922.922 0 0 1 .579.853v.129A2.935 2.935 0 0 0 14.932 31h2.136A2.935 2.935 0 0 0 20 28.068v-.129a.956.956 0 0 1 1.614-.668l.091.091a2.935 2.935 0 0 0 4.146 0l1.511-1.509a2.938 2.938 0 0 0 0-4.147l-.091-.091a.935.935 0 0 1-.185-1.035.924.924 0 0 1 .854-.58h.128A2.935 2.935 0 0 0 31 17.068v-2.136A2.935 2.935 0 0 0 28.068 12ZM29 17.068a.933.933 0 0 1-.932.932h-.128a2.956 2.956 0 0 0-2.083 5.028l.09.091a.934.934 0 0 1 0 1.319l-1.511 1.509a.932.932 0 0 1-1.318 0l-.09-.091A2.957 2.957 0 0 0 18 27.939v.129a.933.933 0 0 1-.932.932h-2.136a.933.933 0 0 1-.932-.932v-.129a2.951 2.951 0 0 0-5.028-2.082l-.091.091a.934.934 0 0 1-1.318 0l-1.51-1.509a.934.934 0 0 1 0-1.319l.091-.091A2.956 2.956 0 0 0 4.06 18h-.128A.933.933 0 0 1 3 17.068v-2.136A.933.933 0 0 1 3.932 14h.128a2.956 2.956 0 0 0 2.083-5.028l-.09-.091a.933.933 0 0 1 0-1.318l1.51-1.511a.932.932 0 0 1 1.318 0l.09.091A2.957 2.957 0 0 0 14 4.061v-.129A.933.933 0 0 1 14.932 3h2.136a.933.933 0 0 1 .932.932v.129a2.956 2.956 0 0 0 5.028 2.082l.091-.091a.932.932 0 0 1 1.318 0l1.51 1.511a.933.933 0 0 1 0 1.318l-.091.091A2.956 2.956 0 0 0 27.94 14h.128a.933.933 0 0 1 .932.932Z"></path>
                            <path d="M16 9a7 7 0 1 0 7 7 7.008 7.008 0 0 0-7-7Zm0 12a5 5 0 1 1 5-5 5.006 5.006 0 0 1-5 5Z"></path>
                        </g>
                    </svg>
                </router-link>
            </li>

        </ul>

        <Vue3Lottie id="lottie-done" :animationData="AnimationTasksColored" :noMargin="true" :loop="false" />

        <h1 class="no-select">{{ $t('general.title') }}</h1>

        <List id="list-tasks">

            <li @click="btnClickAddTask" style="color: var(--tg-theme-button-color);cursor: pointer;">
                <div>
                    <svg class="material-only" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="fill: var(--tg-theme-button-color);">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M20 10C20 15.5228 15.5228 20 10 20C4.47715 20 0 15.5228 0 10C0 4.47715 4.47715 0 10 0C15.5228 0 20 4.47715 20 10ZM10 15.6667C9.2 15.6667 9 15 9 14.6667V11H5.33333C5 11 4.33333 10.8 4.33333 10C4.33333 9.2 5 9 5.33333 9H9V5.33333C9 5 9.2 4.33333 10 4.33333C10.8 4.33333 11 5 11 5.33333V9H14.6667C15 9 15.6667 9.2 15.6667 10C15.6667 10.8 15 11 14.6667 11H11V14.6667C11 15 10.8 15.6667 10 15.6667Z" />
                    </svg>

                    <svg class="apple-only" width="16" height="16" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" style="stroke: var(--tg-theme-button-color);">
                        <path d="M7 0V14M0 7H14" stroke-width="1.995" />
                    </svg>

                    <span class="no-select">{{ $t('home.add_task') }}</span>
                </div>
            </li>

            <li v-if="addingTask" id="adding-task">
                <div>
                    <Checkbox :disabled="true" />
                    <input type="text" enterkeyhint="done" v-model="addingTaskTitleText" ref="addingTaskTitle" @focusout="addingTask = false" @keyup.enter="addTask" />
                </div>
            </li>

            <li v-for="(task, taskID) in tasksList" class="item-task" v-touch:swipe="taskSwipeHandler" :key="taskID">
                <div>
                    <Checkbox v-model:checked="task.done" />
                    <input type="text" enterkeyhint="done" :value="task.title" @keyup.enter="taskKeyupHandler" @change="taskUpdateHandler" :data-task-id="taskID" />
                </div>

                <div>
                    <div class="action-delete" @click="btnClickDeleteTask(taskID)">
                        <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg" style="fill: var(--tele-vue-danger-button-text-color);">
                            <path d="M6.54297 15.8423C6.38802 15.8423 6.26351 15.798 6.16943 15.7095C6.07536 15.6209 6.02555 15.502 6.02002 15.3525L5.771 6.479C5.76546 6.33512 5.80973 6.21891 5.90381 6.13037C6.00342 6.0363 6.1307 5.98926 6.28564 5.98926C6.43506 5.98926 6.5568 6.03353 6.65088 6.12207C6.75049 6.21061 6.80029 6.32682 6.80029 6.4707L7.05762 15.3525C7.05762 15.4964 7.01058 15.6154 6.9165 15.7095C6.82243 15.798 6.69792 15.8423 6.54297 15.8423ZM9 15.8423C8.84505 15.8423 8.71777 15.798 8.61816 15.7095C8.51855 15.6154 8.46875 15.4964 8.46875 15.3525V6.479C8.46875 6.33512 8.51855 6.21891 8.61816 6.13037C8.71777 6.0363 8.84505 5.98926 9 5.98926C9.16048 5.98926 9.29053 6.0363 9.39014 6.13037C9.48975 6.21891 9.53955 6.33512 9.53955 6.479V15.3525C9.53955 15.4964 9.48975 15.6154 9.39014 15.7095C9.29053 15.798 9.16048 15.8423 9 15.8423ZM11.4653 15.8423C11.3049 15.8423 11.1776 15.798 11.0835 15.7095C10.9894 15.6154 10.9451 15.4964 10.9507 15.3525L11.1997 6.479C11.2052 6.32959 11.255 6.21061 11.3491 6.12207C11.4432 6.03353 11.5649 5.98926 11.7144 5.98926C11.8748 5.98926 12.0021 6.0363 12.0962 6.13037C12.1903 6.21891 12.2345 6.33512 12.229 6.479L11.98 15.3525C11.9744 15.502 11.9246 15.6209 11.8306 15.7095C11.7365 15.798 11.6147 15.8423 11.4653 15.8423ZM5.35596 3.82275V1.96338C5.35596 1.36572 5.53857 0.895345 5.90381 0.552246C6.27458 0.203613 6.77816 0.0292969 7.41455 0.0292969H10.5688C11.2052 0.0292969 11.7061 0.203613 12.0713 0.552246C12.4421 0.895345 12.6274 1.36572 12.6274 1.96338V3.82275H11.3076V2.04639C11.3076 1.81396 11.2301 1.62581 11.0752 1.48193C10.9258 1.33805 10.7266 1.26611 10.4775 1.26611H7.50586C7.25684 1.26611 7.05485 1.33805 6.8999 1.48193C6.75049 1.62581 6.67578 1.81396 6.67578 2.04639V3.82275H5.35596ZM1.5874 4.48682C1.42139 4.48682 1.27474 4.42594 1.14746 4.3042C1.02572 4.17692 0.964844 4.02751 0.964844 3.85596C0.964844 3.68994 1.02572 3.54606 1.14746 3.42432C1.27474 3.29704 1.42139 3.2334 1.5874 3.2334H16.4209C16.5869 3.2334 16.7308 3.29427 16.8525 3.41602C16.9743 3.53776 17.0352 3.68441 17.0352 3.85596C17.0352 4.02751 16.9743 4.17692 16.8525 4.3042C16.7363 4.42594 16.5924 4.48682 16.4209 4.48682H1.5874ZM5.19824 18.5151C4.60059 18.5151 4.11637 18.3381 3.74561 17.9839C3.38037 17.6353 3.18392 17.1621 3.15625 16.5645L2.56689 4.3291H3.87012L4.45947 16.415C4.47054 16.6585 4.55632 16.8605 4.7168 17.021C4.87728 17.1815 5.0765 17.2617 5.31445 17.2617H12.6772C12.9207 17.2617 13.1227 17.1815 13.2832 17.021C13.4437 16.866 13.5295 16.6641 13.5405 16.415L14.0967 4.3291H15.4331L14.8521 16.5562C14.8244 17.1538 14.6252 17.6297 14.2544 17.9839C13.8836 18.3381 13.4022 18.5151 12.8101 18.5151H5.19824Z" />
                        </svg>
                    </div>
                </div>
            </li>

            <li v-if="Object.keys(tasks).length > visibletasksCount && !(showAllTasks)" style="color: var(--tg-theme-button-color);" @click="showAllTasks = true">
                <div>
                    <svg width="18" height="10" viewBox="0 0 19 11" fill="none" xmlns="http://www.w3.org/2000/svg" style="fill: var(--tg-theme-button-color);">
                        <path d="M9.50003 11L19 2.17177L16.9712 0L9.50003 7.13773L2.02881 0L-2.41856e-05 2.17177L9.50003 11Z" />
                    </svg>

                    <span class="no-select">{{ $t('home.show_n_more_task', { count: (Object.keys(tasks).length - visibletasksCount) }) }}</span>
                </div>
            </li>

        </List>

        <div v-if="Object.keys(tasksList).length === 0 && !(addingTask)" id="no-tasks" class="no-select">
            {{ $t('home.no_tasks') }}
        </div>

        <p v-else-if="!(addingTask)" class="no-select">{{ $t('home.swipe_to_delete_hint') }}</p>
        
    </div>
</template>

<style lang="scss">

    body {
        background-color: var(--tg-theme-secondary-bg-color);
    }

    #container-home {
        display: flex;
        flex-direction: column;

        #header-bar {
            display: flex;
            width: 90%;
            margin: 1rem auto 0;
            list-style: none;
            justify-content: flex-end;
            align-items: center;

            > li {
                cursor: pointer;
            }
        }

        #lottie-done {
            width: 50%;
            margin: -1rem auto;
            padding-left: 0.75rem;
        }

        h1 {
            text-align: center;
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
        }

        #list-tasks, #no-tasks {
            background-color: var(--tg-theme-bg-color);
            margin: 0 auto;
            width: 90%;
            border-radius: 1rem;
            margin-top: 1rem;
            overflow: hidden;
        }

        #list-tasks {
            margin-bottom: 2rem;

            > li {
                > div {
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                }
            }
        }

        #no-tasks {
            padding: 1rem;
            text-align: center;
        }

        #adding-task, .item-task {

            input[type=text] {
                width: 100%;
                border: none;
                outline: none;
                caret-color: var(--tg-theme-button-color);
                background: transparent;
                color: var(--tg-theme-text-color);
                font-size: 1rem;
            }

        }

        .item-task {
            position: relative;

            > div {
                transition: all .125s ease-in-out;

                &:first-child {
                    width: 100%;
                }

                &:last-child {
                    position: absolute;
                    height: 100%;
                    left: 100%;
                    width: 100%;
                    top: 0;
                    display: flex;

                    > div.action-delete {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        height: 100%;
                        background-color: var(--tele-vue-danger-button-color);
                        color: var(--tele-vue-danger-button-text-color);
                        font-size: 0.925rem;
                        width: 15%;
                    }
                }
            }

            &.swiped {
                > div {
                    transform: translateX(-15%);
                }
            }
        }

        > p {
            margin: 0 auto;
            width: 90%;
            padding: 0 1rem;
            color: var(--tg-theme-hint-color);
        }
    }

    .no-select {
        user-select: none;
        -moz-user-select: none;
        -webkit-user-select: none;
    }

    body.rtl {

        .item-task {

            > div {
                &:last-child {
                    left: auto;
                    right: 100%;
                }
            }

            &.swiped {
                > div {
                    transform: translateX(15%) !important;
                }
            }
        }

    }

</style>