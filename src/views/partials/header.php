<header class="header-menu">
    <nav class="navbar">
        <ul class="navbar-menu">
            
            <div class="main-menu">
                <li><a href="/profile" class="navbar-link">Profile</a></li>
                <li><a href="/events" class="navbar-link">Events</a></li>
                <li><a href="/transactions" class="navbar-link">Transactions</a></li>
                <li><a href="/bets" class="navbar-link">Bets</a></li>
            </div>

            <div class="right-menu">
                <li style="margin-right:1rem"><a href="/transactions" class="navbar-link"> 
                    <?= $this->session->get('login') ?> : <?= $this->session->get('balance') / 100 . ' ' . $this->session->get('currency') ?></a></li>
                <li><a href="/logout" class="navbar-link button-link">Logout</a></li>
            </div>
        </ul>
    </nav>
</header>

<style>
    .header-menu {
        background-color: #333;
        color: white;
        padding: 20px;
        text-align: center;
    }

    .navbar {
        padding: 10px 0;
    }

    .navbar-menu {
        list-style-type: none;
        padding: 0;
        margin: 0;
        display: flex;
        justify-content: space-between; 
        width: 100%;
    }

    .navbar-link {
        text-decoration: none;
        color: white;
        padding: 12px 20px;
        font-size: 1rem;
        transition: background-color 0.3s ease;
    }

    .navbar-link:hover {
        background-color: #555;
        border-radius: 5px;
    }

   
    .main-menu {
        display: flex;
        justify-content: center;
        flex-grow: 1;
    }

    
    .right-menu {
        display: flex;
        align-items: center;
    }

    @media screen and (max-width: 768px) {
        .navbar-menu {
            flex-direction: column;
            align-items: center;
        }
        .main-menu {
            flex-direction: column;
            align-items: center;
        }
        .right-menu {
            margin-top: 1rem; 
            align-items: center;
        }
        .navbar-link {
            padding: 10px 15px;
            font-size: 1.2rem;
        }
    }
</style>


