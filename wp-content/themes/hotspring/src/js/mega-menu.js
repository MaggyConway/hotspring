export default ($) => {
  function menuLoader(container, localStorageName, storeTimeName) {
    $.ajax({
      url: `/wp-json/fetch/v1/mainmenu`,
      type: 'GET',
      dataType: 'json',
      global: false,
      async: true,
      beforeSend: function beforeSend() {
        container.addClass('loading');
        container.html('<div class="loader"></div>');
      },
      success: function success(res) {
        if (res.success) {
          container.addClass('loaded');

          var html = '<ul class="mobile-main-menu">';
          var submenuMain = [];
          var collectionMenu = [];

          $.map(res.data.mainMenuItems, function(item, key) {
            html += `<li class="mobile-main-menu__item ${item.links.length !== 0 ? 'has-submenu' : ''}"><a href="${item.link}">${item.title}</a>`;
            if(item.links && item.links.length !== 0) {
              html += '<ul class="mobile-submenu">';
              html += `<li class="mobile-submenu__item mobile-menu-header"><a href="#">${item.title}</a></li>`;
              $.map(item.links, function(submenu, key) {
                if(typeof submenu === 'object' && submenu !== null) {
                  html += `<li class="mobile-submenu__item has-submenu"><a href="${submenu.link}">${submenu.title}</a>`;
                  html += '<ul class="mobile-collection">';
                  html += `<li class="mobile-collection__item mobile-menu-header"><a href="#">${submenu.title}</a></li>`;
                  html += `<li class="mobile-collection__item mobile-collection__info">${submenu.content}</li>`;

                  submenuMain.push({[key]: (submenu.content)});

                  $.map(submenu.links, function(collection, key) {
                    if(collection.class && collection.class === 'view-all') {
                      // html += `<li class="mobile-collection__item mobile-collection__item--view-all"><a href="${collection.link}">View All</a></li>`;
                    } else {
                      html += `<li class="mobile-collection__item"><a href="${collection.link}">${collection.title}</a></li>`;

                      collectionMenu.push({[key]: (collection.content)});
                    }
                  });
                  html += '</ul>';
                  html += '</li>';
                } else {
                  html += `<li class="mobile-submenu__item"><a href="${submenu}">${key}</a></li>`;
                }
              });
              html += '</ul>';
            }
            html += '</li>';
          });

          html += '</ul>';

          container.html(html);
          localStorage.setItem(localStorageName, html);
          localStorage.setItem(storeTimeName, `${new Date().getDate()}.${new Date().getMonth()}`);
          localStorage.setItem('submenuMain', JSON.stringify(submenuMain));
          localStorage.setItem('collectionMenu', JSON.stringify(collectionMenu));
          mobileMenuHandlers(container);
        }
      },
      complete: function complete() {
        container.removeClass('loading');
        container.find('.loader').remove();
      }
    });
  };

  function mobileMenuHandlers (container) {
    var hasSubmenu = container.find('.has-submenu > a');
    var mobileBack = container.find('.mobile-menu-header > a');

    hasSubmenu.on('click', function (e) {
      e.preventDefault();
      $(this).parent().addClass('is-active');
      $(this).next().addClass('is-active');
    });

    mobileBack.on('click', function (e) {
      e.preventDefault();
      $(this).parent().parent().removeClass('is-active');
      $(this).parent().parent().parent().removeClass('is-active');
    });
  };

  function mobileMenuRender () {
    var containerMenu = $('.mobile-panel__menu');
    var localStorageName = 'mobile-panel';
    var storeTimeName = 'expiry-time-menu';
    var expiryTimeStore = localStorage.getItem(storeTimeName);
    var nowTime = `${new Date().getDate()}.${new Date().getMonth()}`;

    if(!containerMenu.hasClass('loaded')) {
      if (localStorage.getItem(localStorageName) !== null) {
        if(expiryTimeStore !== null) {
          if(expiryTimeStore === nowTime) {
            containerMenu.html(localStorage.getItem(localStorageName));
            containerMenu.addClass('loaded');
            mobileMenuHandlers(containerMenu);
          } else {
            menuLoader(containerMenu, localStorageName, storeTimeName);
          }
        } else {
          localStorage.setItem(storeTimeName, nowTime);
          containerMenu.html(localStorage.getItem(localStorageName));
          containerMenu.addClass('loaded');
          mobileMenuHandlers(containerMenu);
        }
      } else {
        menuLoader(containerMenu, localStorageName, storeTimeName);
      }
    }
  };

  function itemsRender(dataAttr, container, storage) {
    var items = JSON.parse(storage);
    $.map(items, function(item) {
      $.map(item, function(value, key) {
        if(dataAttr === key) {
          container.addClass('loaded');
          container.html(value);
        }
      });
    });
  }

  $('.submenu > .menu__item > a').each(function() {
    var link = $(this);
    var dataAttr = link.attr('data-hover-load');
    var contentWrap = $(this).siblings('.collection__panel');
    var storage = localStorage.getItem('submenuMain');

    if(dataAttr && !contentWrap.hasClass('loaded')) {
      link.mouseenter(function() {
        if (storage !== null) {
          itemsRender(dataAttr, contentWrap, storage);
        } else {
          mobileMenuRender();
          itemsRender(dataAttr, contentWrap, localStorage.getItem('submenuMain'));
        }
      });
    }
  });

  $('.collection__model').each(function() {
    var link = $(this).find('a');
    var dataAttr = link.attr('data-hover-load');
    var contentWrap = $(this).find('.collection__info');
    var storage = localStorage.getItem('collectionMenu');

    if(dataAttr && !contentWrap.hasClass('loaded')) {
      link.mouseenter(function() {
        if (storage !== null) {
          itemsRender(dataAttr, contentWrap, storage);
        } else {
          mobileMenuRender();
          itemsRender(dataAttr, contentWrap, localStorage.getItem('collectionMenu'));
        }
      });
    }
  });

  mobileMenuRender();
}
