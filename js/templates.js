/**
 * Lightweight template engine
 * Spiridonov N.
 *
 * Usage:
 * HTML:
 * <script type="text/template" data-template="template">
 *   <div class="template">${id}</div>
 * </script>
 * JS:
 * var t = new Templates();
 * var items = [{id: 1}, {id: 2}];
 * t.insert('body', 'template', items);
 * @constructor
 */

function Templates() {
  this.template = {};
}

Templates.prototype.load = function load(name) {
  try {
    this.template[name] = document.querySelector('script[data-template="' + name + '"]').innerHTML.split(/\${(.+?)}/g);
  } catch (e) {
    console.error('Loading ' + name, e);
  }
};

Templates.prototype.render = function render(props) {
  return function (tok, i) {
    return (i % 2) ? props[tok] : tok;
  };
};

Templates.prototype.insert = function insert(container, template, items) {
  
  if ( !this.template[template] ) this.load(template);

  try {
    var html = items.map(function (item) {
      return this.template[template].map(this.render(item)).join('');
    }.bind(this)) || [];

    document.querySelector(container).innerHTML = html.join('');
  } catch (e) {
    console.error('Inserting ' + template, e);
    console.error('Items', items);
  }
};

Templates.prototype.append = function append(container, template, item) {
  if ( !this.template[template] ) this.load(template);

  try {
    document.querySelector(container).insertAdjacentHTML('beforeend',
      this.template[template].map(this.render(item)).join(''));
  } catch (e) {
    console.error('Inserting ' + template, e);
    console.error('Items', item);
  }
};

Templates.prototype.html = function html(template,items)
{
  if ( !this.template[template] ) this.load(template);

  try {
    var html = items.map(function (item) {
      return this.template[template].map(this.render(item)).join('');
    }.bind(this)) || [];

    return  html.join('');
  } catch (e) {
    console.error('Inserting ' + template, e);
    console.error('Items', items);
  }    
};