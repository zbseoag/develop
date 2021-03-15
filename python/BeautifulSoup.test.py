from bs4 import BeautifulSoup
import requests

html = """
<div class="flex-auto min-width-0 col-md-2" role="rowheader">
    <a class="js-navigation-open d-block py-2 px-3" href="/docker-library/php/tree/master/8.0/buster" rel="nofollow"
        title="Go to parent directory">
        <span class="text-bold text-center d-inline-block" style="min-width: 16px;">. .</span>
    </a>
</div>
<div class="flex-auto min-width-0 col-md-2 mr-3" role="rowheader">
    <span class="css-truncate css-truncate-target d-block width-fit">
    <a class="js-navigation-open link-gray-dark"
            href="/docker-library/php/blob/master/8.0/buster/fpm/Dockerfile" title="Dockerfile">Dockerfile</a></span>
</div>

 <div class="flex-auto min-width-0 col-md-2 mr-3" role="rowheader">
    <span class="css-truncate css-truncate-target d-block width-fit"><a class="js-navigation-open link-gray-dark"
            href="/docker-library/php/blob/master/8.0/buster/fpm/docker-php-entrypoint"
            title="docker-php-entrypoint">docker-php-entrypoint</a></span>
</div>, <div class="flex-auto min-width-0 col-md-2 mr-3" role="rowheader">
    <span class="css-truncate css-truncate-target d-block width-fit"><a class="js-navigation-open link-gray-dark"
            href="/docker-library/php/blob/master/8.0/buster/fpm/docker-php-ext-configure"
            title="docker-php-ext-configure">docker-php-ext-configure</a></span>
</div>, <div class="flex-auto min-width-0 col-md-2 mr-3" role="rowheader">
    <span class="css-truncate css-truncate-target d-block width-fit"><a class="js-navigation-open link-gray-dark"
            href="/docker-library/php/blob/master/8.0/buster/fpm/docker-php-ext-enable"
            title="docker-php-ext-enable">docker-php-ext-enable</a></span>
</div>, <div class="flex-auto min-width-0 col-md-2 mr-3" role="rowheader">
    <span class="css-truncate css-truncate-target d-block width-fit"><a class="js-navigation-open link-gray-dark"
            href="/docker-library/php/blob/master/8.0/buster/fpm/docker-php-ext-install"
            title="docker-php-ext-install">docker-php-ext-install</a></span>
</div>, <div class="flex-auto min-width-0 col-md-2 mr-3" role="rowheader">
    <span class="css-truncate css-truncate-target d-block width-fit"><a class="js-navigation-open link-gray-dark"
            href="/docker-library/php/blob/master/8.0/buster/fpm/docker-php-source"
            title="docker-php-source">docker-php-source</a></span>
</div>
"""


all = soup.find_all(True)
for tag in all:
    print(tag.name)


soup.p['class']
soup.p.get('class')
soup.p['class']="newClass"

type(soup.a.string)

soup.head.contents[0]

for child in  soup.body.children:
    print child

#所有子节点
for child in soup.descendants:
    print child


for string in soup.strings:
    print(repr(string))

#去除多余空白
for string in soup.stripped_strings:
    print(repr(string))


#所有你节点
content = soup.head.title.string
for parent in  content.parents:
    print parent.name

前后兄弟节点
.next_sibling .next_siblings .previous_sibling .previous_siblings

前后节点，不分层次
.next_element .previous_element .next_elements .previous_elements

find_all( name , attrs , recursive , string , **kwargs )
soup.find_all(["a", "b"])

#自定义过滤器
def has_class_but_no_id(tag):
    return tag.has_attr('class') and not tag.has_attr('id')
soup.find_all(has_class_but_no_id)

soup.find_all(id='link2')

#匹配带 .com
soup.find_all(href=re.compile(".com"))

soup.find_all("a", class_="sister")
data_soup.find_all(data-foo="value")

data_soup.find_all(attrs={"data-foo": "value"})

#innerHtml
soup.find_all(text="Elsie")
soup.find_all("a", limit=2， recursive=False)

soup.get_text()
soup.prettify()






# num = re.sub(r'<div class="pen-l">.*>\s+返回列表\s+</a>', '<div class="pen-l">', content.prettify(), flags=re.S)
# print(num)