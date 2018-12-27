<main class="main-content">
    <div class="page">
        <div class="container">
            <div class="text-center">
                <div class="filter-links filterable-nav">
                    <select class="mobile-filter">
                        <option value="*">Show all</option>
                        <option value=".hair">hair</option>
                        <option value=".manicure">manicure</option>
                        <option value=".pedicure">pedicure</option>
                        <option value=".face">face</option>
                        <option value=".makeup">makeup</option>
                    </select>
                    <a href="#" class="current wow fadeInRight" data-filter="*">Show all</a>
                    <a href="#" class="wow fadeInRight" data-wow-delay=".2s" data-filter=".hair">hair</a>
                    <a href="#" class="wow fadeInRight" data-wow-delay=".4s" data-filter=".manicure">manicure</a>
                    <a href="#" class="wow fadeInRight" data-wow-delay=".6s" data-filter=".pedicure">pedicure</a>
                    <a href="#" class="wow fadeInRight" data-wow-delay=".8s" data-filter=".face">face</a>
                    <a href="#" class="wow fadeInRight" data-wow-delay="1s" data-filter=".makeup">makeup</a>
                </div>
            </div>

            <div class="filterable-items">
            <?php $arrayType = ["manicure", "face", "hair", "pedicure", "makeup"];
            for ($i=1; $i<=12; $i++){
                $type = array_rand($arrayType);
            ?>
                <div class="gallery-item filterable-item <?=$arrayType[$type]?>">
                    <a href="public/dummy/large-gallery/gallery-<?=$i?>.jpg">
                        <figure class="featured-image">
                            <img src="public/dummy/gallery-<?=$i?>.jpg" alt=""/>
                            <figcaption>
                                <h2 class="gallery-title">Lorem ipsum dolor sit amet</h2>
                                <p>Maecenas dictum suscipit</p>
                            </figcaption>
                        </figure>
                    </a>
                </div>
            <?php } ?>
            </div>
        </div>
    </div>
</main>
