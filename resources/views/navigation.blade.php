<h3 class="flex items-center font-normal text-white mb-6 text-base no-underline">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="sidebar-icon">
        <path fill="var(--sidebar-icon)" class="heroicon-ui" d="M20 6a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6c0-1.1.9-2 2-2h7.41l2 2H20zM4 6v12h16V8h-7.41l-2-2H4z"/>
    </svg>
    <span class="sidebar-label">
        Page manager
    </span>
</h3>

<ul class="list-reset mb-8">
    <li class="leading-wide mb-4 text-sm">
        <router-link :to="{
            name: 'index',
            params: {
                resourceName: 'pages'
            }
        }" class="text-white ml-8 no-underline dim">
            Pages
        </router-link>
    </li>

    <li class="leading-wide mb-4 text-sm">
        <router-link :to="{
            name: 'index',
            params: {
                resourceName: 'regions'
            }
        }" class="text-white ml-8 no-underline dim">
            Regions
        </router-link>
    </li>
</ul>