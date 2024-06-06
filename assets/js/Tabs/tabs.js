$(function () {

	$('nav.tabs__nav').on('click', 'button:not(.active)', function () {
		$(this).addClass('active').siblings().removeClass('active')
			.closest('div.tabs').find('div.tabs__content-item').removeClass('active')
			.eq($(this).index()).addClass('active');
    })

})
