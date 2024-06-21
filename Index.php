<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>مجمّع الأخبار</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.4.2/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100 font-sans">
  <header class="bg-white shadow">
    <div class="container mx-auto px-4 py-6 flex justify-between items-center">
      <h1 class="text-3xl font-bold">مجمّع الأخبار</h1>
      <nav>
        <ul class="flex space-x-4">
          <li><a href="#" class="text-gray-600 hover:text-gray-900">الرئيسية</a></li>
          <li><a href="#" class="text-gray-600 hover:text-gray-900">الموضوعات الساخنة</a></li>
          <li><a href="#" class="text-gray-600 hover:text-gray-900">تغذيات الدول</a></li>
        </ul>
      </nav>
      <div x-data="{ search: '', results: [] }" class="relative">
        <input type="text" x-model="search" @input.debounce="fetchResults" placeholder="ابحث..." class="border rounded py-2 px-3">
        <ul x-show="results.length > 0" class="absolute bg-white shadow-lg border rounded w-full mt-1">
          <template x-for="result in results">
            <li><a :href="result.link" class="block py-2 px-3 hover:bg-gray-100" x-text="result.title"></a></li>
          </template>
        </ul>
      </div>
    </div>
  </header>

  <main class="container mx-auto px-4 py-6">
    <section x-data="fetchNews('https://omannews.gov.om/rss.ona')" class="mb-8">
      <h2 class="text-2xl font-bold mb-4">الموضوعات الساخنة</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="item in items">
          <div class="bg-white p-4 rounded shadow">
            <h3 class="text-xl font-semibold mb-2"><a :href="item.link" x-text="item.title"></a></h3>
            <p class="text-gray-600 mb-2" x-text="item.source"></p>
            <p class="text-gray-600 mb-2" x-text="item.pubDate"></p>
            <p class="text-gray-600" x-text="item.category"></p>
          </div>
        </template>
      </div>
    </section>

    <section x-data="fetchNews('https://www.bna.bh/en/GenerateRssFeed.aspx?categoryId=153')">
      <h2 class="text-2xl font-bold mb-4">تغذيات الدول</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="item in items">
          <div class="bg-white p-4 rounded shadow">
            <h3 class="text-xl font-semibold mb-2"><a :href="item.link" x-text="item.title"></a></h3>
            <p class="text-gray-600 mb-2" x-text="item.source"></p>
            <p class="text-gray-600 mb-2" x-text="item.pubDate"></p>
            <p class="text-gray-600" x-text="item.category"></p>
          </div>
        </template>
      </div>
    </section>
  </main>

  <script>
    function fetchNews(url) {
      return {
        items: [],
        init() {
          fetch(url)
            .then(response => response.text())
            .then(data => {
              let parser = new DOMParser();
              let xml = parser.parseFromString(data, "application/xml");
              let items = [...xml.querySelectorAll("item")].map(item => ({
                title: item.querySelector("title").textContent,
                link: item.querySelector("link").textContent,
                pubDate: item.querySelector("pubDate").textContent,
                category: item.querySelector("category") ? item.querySelector("category").textContent : "عام",
                source: item.querySelector("source") ? item.querySelector("source").textContent : "غير معروف"
              }));
              this.items = items;
            });
        }
      }
    }

    function fetchResults() {
      fetch(`https://api.example.com/search?q=${this.search}`)
        .then(response => response.json())
        .then(data => {
          this.results = data.results;
        });
    }
  </script>
</body>
</html>
