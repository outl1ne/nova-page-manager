import PageManagerField from './components/PageManagerField';
import PrefixSlugField from './components/PrefixSlugField';

let pageManagerDarkModeObserver = null;

Nova.booting((Vue, router, store) => {
  pageManagerDarkModeObserver = new MutationObserver(() => {
    const cls = document.documentElement.classList;
    const isDarkMode = cls.contains('dark');

    if (isDarkMode && !cls.contains('npm-dark')) {
      cls.add('npm-dark');
    } else if (!isDarkMode && cls.contains('npm-dark')) {
      cls.remove('npm-dark');
    }
  }).observe(document.documentElement, {
    attributes: true,
    attributeOldValue: true,
    attributeFilter: ['class'],
  });

  Vue.component('form-page-manager-field', PageManagerField);
  Vue.component('detail-page-manager-field', PageManagerField);
  Vue.component('form-prefix-slug-field', PrefixSlugField);
});
