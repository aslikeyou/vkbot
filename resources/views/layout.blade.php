<html>
<body>
    <div class="header" style="border: 1px solid gray">
        <ul>
            <li>
                <a href="/">Main</a>
            </li>
            <li>
                <a href="/steal">Steal post</a>
            </li><li>
                <a href="/watch">Watch posts</a>
            </li>
            <li>
                <a href="/mygroups">My groups</a>
            </li>
            <li>
                <a href="/watch_groups">Other groups</a>
            </li>
            <li>
                <a href="/watch_relations">Relations</a>
            </li>
        </ul>
    </div>
    <div>
        @yield('subnav')
    </div>
    <div class="content" style="border: 1px solid goldenrod">
        @yield('content')
    </div>
</body>
</html>