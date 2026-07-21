<!-- TOPBAR -->
<header class="topbar">
    <button class="menu-toggle" onclick="toggleSidebar()"><i class="fa-solid fa-bars"></i></button>
    <div class="topbar-greet">Hi, <span>{{ Auth::check() ? Auth::user()->name : 'User' }}!</span> 👋</div>
    <div class="quote-ticker">
        <div class="quote-text">"Your limitation — it's only your imagination." &nbsp;&nbsp;&nbsp; "Push yourself, because no one else is going to do it for you." &nbsp;&nbsp;&nbsp; "Great things never come from comfort zones."</div>
    </div>
    <div class="topbar-actions">
        <button class="date-btn" onclick="toggleDateMenu()" id="dateBtn">
            <i class="fa-regular fa-calendar"></i>
            <span id="dateBtnLabel">Today</span>
            <i class="fa-solid fa-chevron-down" style="font-size:9px;"></i>
        </button>
        <button class="icon-btn">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
        <button class="icon-btn" style="position:relative;">
            <i class="fa-solid fa-bell"></i>
            <span class="notif-dot"></span>
        </button>
        <button class="icon-btn">
            <i class="fa-solid fa-gear"></i>
        </button>
    </div>
</header>
