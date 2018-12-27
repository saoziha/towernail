<?php

    function slider(){
        for ($i = 1; $i<=8; $i++){
            echo
            "<li id='slideimg-$i' data-bg-image='public/towernail/shop-$i.jpg'>
                <div class='box'>
                    <div class='container'>
                        <h3 class='slider-subtitle'>Welcome to</h3>
                        <h2 class='slider-title'>TOWER NAILS &amp; BEAUTY</h2>
                        <p>Come to Tower Nails &amp; Beauty for better care. We understand your needs.</p>
                        <a href='service.html' class='button large'>Read more</a>
                    </div>
                </div>
            </li>";
        }
    }

?>
