/**
 * 侧边栏滚动功能
 */
(function() {
  'use strict';
  
  // 初始化所有滚动容器
  function initSidebarScroll() {
    const containers = document.querySelectorAll('.sidebar-scroll-container');
    
    containers.forEach(function(container) {
      const wrapper = container.querySelector('.sidebar-scroll-wrapper');
      const prevBtn = container.querySelector('.sidebar-scroll-prev');
      const nextBtn = container.querySelector('.sidebar-scroll-next');
      
      if (!wrapper || !prevBtn || !nextBtn) return;
      
      // 计算每屏宽度（100%）
      const screenWidth = wrapper.offsetWidth;
      
      // 更新按钮状态
      function updateButtons() {
        const scrollLeft = wrapper.scrollLeft;
        const maxScroll = wrapper.scrollWidth - wrapper.offsetWidth;
        
        prevBtn.disabled = scrollLeft <= 0;
        nextBtn.disabled = scrollLeft >= maxScroll - 1; // 减1避免浮点数误差
      }
      
      // 上一屏
      prevBtn.addEventListener('click', function() {
        wrapper.scrollBy({
          left: -screenWidth,
          behavior: 'smooth'
        });
      });
      
      // 下一屏
      nextBtn.addEventListener('click', function() {
        wrapper.scrollBy({
          left: screenWidth,
          behavior: 'smooth'
        });
      });
      
      // 监听滚动事件
      wrapper.addEventListener('scroll', updateButtons);
      
      // 初始化按钮状态
      updateButtons();
      
      // 监听窗口大小变化
      let resizeTimer;
      window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
          updateButtons();
        }, 100);
      });
    });
  }
  
  // 初始化
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSidebarScroll);
  } else {
    initSidebarScroll();
  }
})();

