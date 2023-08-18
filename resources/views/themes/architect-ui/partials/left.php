<div class="app-sidebar sidebar-shadow <?= $color ? $color : "bg-heavy-rain sidebar-text-dark" ?>">
    <div class="app-header__logo">
        <div class="logo-src"></div>
        <div class="header__pane ml-auto">
            <div>
                <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>

    <div class="app-header__mobile-menu">
        <div>
            <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>
            </button>
        </div>
    </div>

    <div class="app-header__menu">
        <span>
            <button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                <span class="btn-icon-wrapper">
                    <i class="fa fa-ellipsis-v fa-w-6"></i>
                </span>
            </button>
        </span>
    </div>
    
    <div class="scrollbar-container"></div>
    <div class="scrollbar-sidebar">
        <div class="app-sidebar__inner">
            <ul class="vertical-nav-menu">
                <?php $nextItem = 0; ?>
                <?php foreach($menu as $item): ?>
                <?php $nextItem++; ?>
                <?php if($item['type'] == 'heading'): ?>
                <li class="app-sidebar__heading"><?= $item['desc'] ?></li>
                <?php if($item['level'] > $menu[$nextItem]['level'] && $item['level'] != 1): ?>
                <?php for($i = 0; $i < $item['level'] - $menu[$nextItem]['level']; $i++): ?>
                </ul>
                </li>
                <?php endfor ?>
                <?php endif ?>
                <?php elseif($item['type'] == 'item'): ?>
                <li>
                    <a href="<?= strpos($item['url'], "http") !== false ? $item['url'] : url($item['url']) ?>" 
                        class="<?= $active == url($item['url']) ? 'mm-active' : '' ?>">
                        <i class="<?= $item['icon'] ?>"></i>
                        <?= $item['desc'] ?>
                        <?php if(isset($menu[$nextItem]['level']) && $item['level'] < $menu[$nextItem]['level']): ?>
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                        <?php endif ?>
                    </a>
                    <?php if(isset($menu[$nextItem]['level']) && $item['level'] < $menu[$nextItem]['level']): ?>
                    <ul>
                    <?php elseif(isset($menu[$nextItem]['level']) && $item['level'] > $menu[$nextItem]['level'] && $item['level'] != 1): ?>
                    </li>
                    <?php for($i = 0; $i < $item['level'] - $menu[$nextItem]['level']; $i++): ?>
                    </ul>
                    </li>
                    <?php endfor ?>
                    <?php else: ?>
                    </li>
                    <?php endif ?>
                <?php endif ?>
                <?php endforeach ?>
            </ul>
        </div>
    </div>
</div>