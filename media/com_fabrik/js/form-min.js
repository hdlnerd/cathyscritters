var FbForm = new Class(
{
  Implements: [Options, Events],
  options: {
    rowid: "",
    admin: false,
    ajax: false,
    primaryKey: null,
    error: "",
    submitOnEnter: false,
    delayedEvents: false,
    updatedMsg: "Form saved",
    pages: [],
    start_page: 0,
    ajaxValidation: false,
    customJsAction: "",
    plugins: [],
    ajaxmethod: "post",
    inlineMessage: true,
    images: {
      alert: "",
      action_check: "",
      ajax_loader: ""
    }
  },
  initialize: function (b, a)
  {
    if (typeOf(a.rowid) === "null")
    {
      a.rowid = ""
    }
    this.id = b;
    this.result = true;
    this.setOptions(a);
    this.plugins = this.options.plugins;
    this.options.pages = $H(this.options.pages);
    this.subGroups = $H(
    {});
    this.currentPage = this.options.start_page;
    this.formElements = $H(
    {});
    this.bufferedEvents = [];
    this.duplicatedGroups = $H(
    {});
    this.fx = {};
    this.fx.elements = [];
    this.fx.validations = {};
    this.setUpAll();
    this._setMozBoxWidths()
  },
  _setMozBoxWidths: function ()
  {
    if (Browser.firefox)
    {
      this.getForm().getElements(".fabrikElementContainer > .displayBox").each(function (c)
      {
        var f = c.getParent().getComputedSize();
        var a = c.getParent().getSize().x - (f.computedLeft + f.computedRight);
        var d = c.getParent().getSize().x === 0 ? 400 : a;
        c.setStyle("width", d + "px");
        var g = c.getElement(".fabrikElement");
        if (typeOf(g) !== "null")
        {
          a = 0;
          c.getChildren().each(function (b)
          {
            if (b !== g)
            {
              a += b.getSize().x
            }
          });
          g.setStyle("width", d - a - 10 + "px")
        }
      })
    }
  },
  setUpAll: function ()
  {
    this.setUp();
    this.winScroller = new Fx.Scroll(window);
    if (this.options.ajax || this.options.submitOnEnter === false)
    {
      this.stopEnterSubmitting()
    }
    this.watchAddOptions();
    $H(this.options.hiddenGroup).each(function (f, e)
    {
      if (f === true && typeOf(document.id("group" + e)) !== "null")
      {
        var g = document.id("group" + e).getElement(".fabrikSubGroup");
        this.subGroups.set(e, g.cloneWithIds());
        this.hideLastGroup(e, g)
      }
    }.bind(this));
    this.repeatGroupMarkers = $H(
    {});
    this.form.getElements(".fabrikGroup").each(function (e)
    {
      var g = e.id.replace("group", "");
      var f = e.getElements(".fabrikSubGroup").length;
      if (f === 1)
      {
        if (e.getElement(".fabrikSubGroupElements").getStyle("display") === "none")
        {
          f = 0
        }
      }
      this.repeatGroupMarkers.set(g, f)
    }.bind(this));
    var a = this.options.editable === true ? "form" : "details";
    var d = this.form.getElement("input[name=rowid]");
    var c = typeOf(d) === "null" ? "" : d.value;
    var b = {
      option: "com_fabrik",
      view: a,
      controller: "form",
      fabrik: this.id,
      rowid: c,
      format: "raw",
      task: "paginate",
      dir: 1
    };
    [".previous-record", ".next-record"].each(function (e, f)
    {
      b.dir = f;
      if (this.form.getElement(e))
      {
        var g = new Request(
        {
          url: "index.php",
          method: this.options.ajaxmethod,
          data: b,
          onComplete: function (h)
          {
            Fabrik.loader.stop("form_" + this.id);
            h = JSON.decode(h);
            this.update(h);
            this.form.getElement("input[name=rowid]").value = h.post.rowid
          }.bind(this)
        });
        this.form.getElement(e).addEvent("click", function (h)
        {
          g.options.data.rowid = this.form.getElement("input[name=rowid]").value;
          h.stop();
          Fabrik.loader.start("form_" + this.id, Joomla.JText._("COM_FABRIK_LOADING"));
          g.send()
        }.bind(this))
      }
    }.bind(this))
  },
  watchAddOptions: function ()
  {
    this.fx.addOptions = [];
    this.getForm().getElements(".addoption").each(function (e)
    {
      var b = e.getParent(".fabrikElementContainer").getElement(".toggle-addoption");
      var c = new Fx.Slide(e,
      {
        duration: 500
      });
      c.hide();
      b.addEvent("click", function (a)
      {
        a.stop();
        c.toggle()
      })
    })
  },
  setUp: function ()
  {
    this.form = this.getForm();
    this.watchGroupButtons();
    this.watchSubmit();
    this.createPages();
    this.watchClearSession()
  },
  getForm: function ()
  {
    this.form = document.id(this.getBlock());
    return this.form
  },
  getBlock: function ()
  {
    return this.options.editable === true ? "form_" + this.id : "details_" + this.id
  },
  addElementFX: function (h, g)
  {
    var f, b, e;
    h = h.replace("fabrik_trigger_", "");
    if (h.slice(0, 6) === "group_")
    {
      h = h.slice(6, h.length);
      b = h;
      f = document.id(h)
    }
    else
    {
      h = h.slice(8, h.length);
      b = "element" + h;
      if (!document.id(h))
      {
        return false
      }
      f = document.id(h).getParent(".fabrikElementContainer")
    }
    if (f)
    {
      var a = (f).get("tag");
      if (a === "li" || a === "td")
      {
        e = new Element("div",
        {
          style: "width:100%"
        }).adopt(f.getChildren());
        f.empty();
        e.inject(f)
      }
      else
      {
        e = f
      }
      var d = {
        duration: 800,
        transition: Fx.Transitions.Sine.easeInOut
      };
      this.fx.elements[b] = {};
      this.fx.elements[b].css = new Fx.Morph(e, d);
      if (typeOf(e) !== "null" && (g === "slide in" || g === "slide out" || g === "slide toggle"))
      {
        this.fx.elements[b].slide = new Fx.Slide(e, d)
      }
      else
      {
        this.fx.elements[b].slide = null
      }
      return this.fx.elements[b]
    }
    return false
  },
  doElementFX: function (h, g, b)
  {
    var a, f, c, e;
    if (b)
    {
      if (b.options.inRepeatGroup)
      {
        var d = h.split("_");
        d[d.length - 1] = b.options.repeatCounter;
        h = d.join("_")
      }
    }
    h = h.replace("fabrik_trigger_", "");
    if (h.slice(0, 6) === "group_")
    {
      h = h.slice(6, h.length);
      if (h.slice(0, 6) === "group_")
      {
        h = h.slice(6, h.length)
      }
      a = h;
      f = true
    }
    else
    {
      f = false;
      h = h.slice(8, h.length);
      a = "element" + h
    }
    c = this.fx.elements[a];
    if (!c)
    {
      c = this.addElementFX("element_" + h, g);
      if (!c)
      {
        return
      }
    }
    e = f ? c.css.element : c.css.element.getParent(".fabrikElementContainer");
    if (e.get("tag") === "td")
    {
      e = e.getChildren()[0]
    }
    switch (g)
    {
      case "show":
        e.fade("show").removeClass("fabrikHide");
        if (f)
        {
          document.id(h).getElements(".fabrikinput").setStyle("opacity", "1")
        }
        break;
      case "hide":
        e.fade("hide").addClass("fabrikHide");
        break;
      case "fadein":
        e.removeClass("fabrikHide");
        if (c.css.lastMethod !== "fadein")
        {
          c.css.element.show();
          c.css.start(
          {
            opacity: [0, 1]
          })
        }
        break;
      case "fadeout":
        if (c.css.lastMethod !== "fadeout")
        {
          c.css.start(
          {
            opacity: [1, 0]
          }).chain(function ()
          {
            c.css.element.hide();
            e.addClass("fabrikHide")
          })
        }
        break;
      case "slide in":
        c.slide.slideIn();
        break;
      case "slide out":
        c.slide.slideOut();
        e.removeClass("fabrikHide");
        break;
      case "slide toggle":
        c.slide.toggle();
        break;
      case "clear":
        this.formElements.get(h).clear();
        break
    }
    c.lastMethod = g;
    Fabrik.fireEvent("fabrik.form.doelementfx", [this])
  },
  watchClearSession: function ()
  {
    if (this.form && this.form.getElement(".clearSession"))
    {
      this.form.getElement(".clearSession").addEvent("click", function (a)
      {
        a.stop();
        this.form.getElement("input[name=task]").value = "removeSession";
        this.clearForm();
        this.form.submit()
      }.bind(this))
    }
  },
  createPages: function ()
  {
    if (this.options.pages.getKeys().length > 1)
    {
      this.options.pages.each(function (c, b)
      {
        var d = new Element("div",
        {
          "class": "page",
          id: "page_" + b
        });
        d.inject(document.id("group" + c[0]), "before");
        c.each(function (e)
        {
          d.adopt(document.id("group" + e))
        })
      });
      var a = this._getButton("submit");
      if (a && this.options.rowid === "")
      {
        a.disabled = "disabled";
        a.setStyle("opacity", 0.5)
      }
      this.form.getElement(".fabrikPagePrevious").disabled = "disabled";
      this.form.getElement(".fabrikPageNext").addEvent("click", function (b)
      {
        this._doPageNav(b, 1)
      }.bind(this));
      this.form.getElement(".fabrikPagePrevious").addEvent("click", function (b)
      {
        this._doPageNav(b, -1)
      }.bind(this));
      this.setPageButtons();
      this.hideOtherPages()
    }
  },
  _doPageNav: function (g, b)
  {
    if (this.options.editable)
    {
      this.form.getElement(".fabrikMainError").addClass("fabrikHide");
      if (typeOf(document.getElement(".tool-tip")) !== "null")
      {
        document.getElement(".tool-tip").setStyle("top", 0)
      }
      var a = Fabrik.liveSite + "index.php?option=com_fabrik&format=raw&task=form.ajax_validate&form_id=" + this.id;
      Fabrik.loader.start("form_" + this.id, Joomla.JText._("COM_FABRIK_VALIDATING"));
      var c = this.options.pages.get(this.currentPage.toInt());
      var h = $H(this.getFormData());
      h.set("task", "form.ajax_validate");
      h.set("fabrik_ajax", "1");
      h.set("format", "raw");
      h = this._prepareRepeatsForAjax(h);
      var f = new Request(
      {
        url: a,
        method: this.options.ajaxmethod,
        data: h,
        onComplete: function (d)
        {
          Fabrik.loader.stop("form_" + this.id);
          d = JSON.decode(d);
          if (b === -1 || this._showGroupError(d, h) === false)
          {
            this.changePage(b);
            this.saveGroupsToDb()
          }
          new Fx.Scroll(window).toElement(this.form)
        }.bind(this)
      }).send()
    }
    else
    {
      this.changePage(b)
    }
    g.stop()
  },
  saveGroupsToDb: function ()
  {
    if (this.options.multipage_save === 0)
    {
      return
    }
    Fabrik.fireEvent("fabrik.form.groups.save.start", [this]);
    if (this.result === false)
    {
      this.result = true;
      return
    }
    var d = this.form.getElement("input[name=format]").value;
    var c = this.form.getElement("input[name=task]").value;
    this.form.getElement("input[name=format]").value = "raw";
    this.form.getElement("input[name=task]").value = "form.savepage";
    var a = Fabrik.liveSite + "index.php?option=com_fabrik&format=raw&page=" + this.currentPage;
    Fabrik.loader.start("form_" + this.id, "saving page");
    var b = this.getFormData();
    b.fabrik_ajax = 1;
    new Request(
    {
      url: a,
      method: this.options.ajaxmethod,
      data: b,
      onComplete: function (e)
      {
        Fabrik.fireEvent("fabrik.form.groups.save.completed", [this]);
        if (this.result === false)
        {
          this.result = true;
          return
        }
        this.form.getElement("input[name=format]").value = d;
        this.form.getElement("input[name=task]").value = c;
        if (this.options.ajax)
        {
          Fabrik.fireEvent("fabrik.form.groups.save.end", [this, e])
        }
        Fabrik.loader.stop("form_" + this.id)
      }.bind(this)
    }).send()
  },
  changePage: function (a)
  {
    this.changePageDir = a;
    Fabrik.fireEvent("fabrik.form.page.change", [this]);
    if (this.result === false)
    {
      this.result = true;
      return
    }
    this.currentPage = this.currentPage.toInt();
    if (this.currentPage + a >= 0 && this.currentPage + a < this.options.pages.getKeys().length)
    {
      this.currentPage = this.currentPage + a;
      if (!this.pageGroupsVisible())
      {
        this.changePage(a)
      }
    }
    this.setPageButtons();
    document.id("page_" + this.currentPage).setStyle("display", "");
    this._setMozBoxWidths();
    this.hideOtherPages();
    Fabrik.fireEvent("fabrik.form.page.chage.end", [this]);
    if (this.result === false)
    {
      this.result = true;
      return
    }
  },
  pageGroupsVisible: function ()
  {
    var a = false;
    this.options.pages.get(this.currentPage).each(function (b)
    {
      var c = document.id("group" + b);
      if (typeOf(c) !== "null")
      {
        if (c.getStyle("display") !== "none")
        {
          a = true
        }
      }
    });
    return a
  },
  hideOtherPages: function ()
  {
    this.options.pages.each(function (b, a)
    {
      if (a.toInt() !== this.currentPage.toInt())
      {
        document.id("page_" + a).setStyle("display", "none")
      }
    }.bind(this))
  },
  setPageButtons: function ()
  {
    var c = this._getButton("submit");
    var b = this.form.getElement(".fabrikPagePrevious");
    var a = this.form.getElement(".fabrikPageNext");
    if (this.currentPage === this.options.pages.getKeys().length - 1)
    {
      if (typeOf(c) !== "null")
      {
        c.disabled = "";
        c.setStyle("opacity", 1)
      }
      a.disabled = "disabled";
      a.setStyle("opacity", 0.5)
    }
    else
    {
      if (typeOf(c) !== "null" && (this.options.rowid === "" || this.options.rowid.toString() === "0"))
      {
        c.disabled = "disabled";
        c.setStyle("opacity", 0.5)
      }
      a.disabled = "";
      a.setStyle("opacity", 1)
    }
    if (this.currentPage === 0)
    {
      b.disabled = "disabled";
      b.setStyle("opacity", 0.5)
    }
    else
    {
      b.disabled = "";
      b.setStyle("opacity", 1)
    }
  },
  addElements: function (b)
  {
    b = $H(b);
    b.each(function (c, a)
    {
      c.each(function (d)
      {
        if (typeOf(d) !== "null")
        {
          this.addElement(d, d.options.element, a)
        }
      }.bind(this))
    }.bind(this));
    b.each(function (a)
    {
      a.each(function (c)
      {
        if (typeOf(c) !== "null")
        {
          try
          {
            c.attachedToForm()
          }
          catch (d)
          {
            fconsole(c.options.element + " attach to form:" + d)
          }
        }
      }.bind(this))
    }.bind(this));
    Fabrik.fireEvent("fabrik.form.elements.added", [this])
  },
  addElement: function (b, a, c)
  {
    a = a.replace("[]", "");
    var d = a.substring(a.length - 3, a.length) === "_ro";
    b.form = this;
    b.groupid = c;
    this.formElements.set(a, b);
    Fabrik.fireEvent("fabrik.form.element.added", [this, a, b]);
    if (d)
    {
      a = a.substr(0, a.length - 3);
      this.formElements.set(a, b)
    }
  },
  dispatchEvent: function (b, a, d, e)
  {
    if (!this.options.delayedEvents)
    {
      var c = this.formElements.get(a);
      if (c && e !== "")
      {
        c.addNewEvent(d, e)
      }
    }
    else
    {
      this.bufferEvent(b, a, d, e)
    }
  },
  bufferEvent: function (b, a, c, d)
  {
    this.bufferedEvents.push([b, a, c, d])
  },
  processBufferEvents: function ()
  {
    this.setUp();
    this.options.delayedEvents = false;
    this.bufferedEvents.each(function (c)
    {
      var a = c[1];
      var b = this.formElements.get(a);
      b.element = document.id(a);
      this.dispatchEvent(c[0], a, c[2], c[3])
    }.bind(this))
  },
  action: function (a, c)
  {
    var b = this.formElements.get(c);
    Browser.exec("oEl." + a + "()")
  },
  triggerEvents: function (a)
  {
    this.formElements.get(a).fireEvents(arguments[1])
  },
  watchValidation: function (c, b)
  {
    if (this.options.ajaxValidation === false)
    {
      return
    }
    var a = document.id(c);
    if (typeOf(a) === "null")
    {
      fconsole("watch validation failed, could not find element " + c);
      return
    }
    if (a.className === "fabrikSubElementContainer")
    {
      a.getElements(".fabrikinput").each(function (d)
      {
        d.addEvent(b, function (f)
        {
          this.doElementValidation(f, true)
        }.bind(this))
      }.bind(this));
      return
    }
    a.addEvent(b, function (d)
    {
      this.doElementValidation(d, false)
    }.bind(this))
  },
  doElementValidation: function (j, b, h)
  {
    var f;
    if (this.options.ajaxValidation === false)
    {
      return
    }
    h = typeOf(h) === "null" ? "_time" : h;
    if (typeOf(j) === "event" || typeOf(j) === "object" || typeOf(j) === "domevent")
    {
      f = j.target.id;
      if (b === true)
      {
        f = document.id(j.target).getParent(".fabrikSubElementContainer").id
      }
    }
    else
    {
      f = j
    }
    if (typeOf(document.id(f)) === "null")
    {
      return
    }
    if (document.id(f).getProperty("readonly") === true || document.id(f).getProperty("readonly") === "readonly")
    {}
    var g = this.formElements.get(f);
    if (!g)
    {
      f = f.replace(h, "");
      g = this.formElements.get(f);
      if (!g)
      {
        return
      }
    }
    Fabrik.fireEvent("fabrik.form.element.validaton.start", [this, g, j]);
    if (this.result === false)
    {
      this.result = true;
      return
    }
    g.setErrorMessage(Joomla.JText._("COM_FABRIK_VALIDATING"), "fabrikValidating");
    var k = $H(this.getFormData());
    k.set("task", "form.ajax_validate");
    k.set("fabrik_ajax", "1");
    k.set("format", "raw");
    k = this._prepareRepeatsForAjax(k);
    var i = f;
    if (g.origId)
    {
      i = g.origId + "_0"
    }
    g.options.repeatCounter = g.options.repeatCounter ? g.options.repeatCounter : 0;
    var c = Fabrik.liveSite + "index.php?option=com_fabrik&form_id=" + this.id;
    var a = new Request(
    {
      url: c,
      method: this.options.ajaxmethod,
      data: k,
      onError: function (e)
      {
      }.bind(this),
      onComplete: function (d)
      {
        this._completeValidaton(d, f, i)
      }.bind(this)
    }).send()
  },
  _completeValidaton: function (c, d, a)
  {
    c = JSON.decode(c);
    this.formElements.each(function (f, e)
    {
      f.afterAjaxValidation()
    });
    Fabrik.fireEvent("fabrik.form.elemnet.validation.complete", [this, c, d, a]);
    if (this.result === false)
    {
      this.result = true;
      return
    }
    var b = this.formElements.get(d);
    if ((c.modified[a] !== undefined))
    {
      b.update(c.modified[a])
    }
    if (typeOf(c.errors[a]) !== "null")
    {
      this._showElementError(c.errors[a][b.options.repeatCounter], d)
    }
    else
    {
      this._showElementError([], d)
    }
  },
  _prepareRepeatsForAjax: function (a)
  {
    this.getForm();
    if (typeOf(a) === "hash")
    {
      a = a.getClean()
    }
    this.form.getElements("input[name^=fabrik_repeat_group]").each(function (b)
    {
      if (b.id.test(/fabrik_repeat_group_\d+_counter/))
      {
        var d = b.name.match(/\[(.*)\]/)[1];
        a["fabrik_repeat_group[" + d + "]"] = b.get("value")
      }
    });
    return a
  },
  _showGroupError: function (e, f)
  {
    var a;
    var b = Array.from(this.options.pages.get(this.currentPage.toInt()));
    var c = false;
    $H(f).each(function (g, d)
    {
      d = d.replace(/\[(.*)\]/, "").replace(/%5B(.*)%5D/, "");
      if (this.formElements.has(d))
      {
        var h = this.formElements.get(d);
        if (b.contains(h.groupid.toInt()))
        {
          if (e.errors[d])
          {
            var i = "";
            if (typeOf(e.errors[d]) !== "null")
            {
              i = e.errors[d].flatten().join("<br />")
            }
            if (i !== "")
            {
              a = this._showElementError(e.errors[d], d);
              if (c === false)
              {
                c = a
              }
            }
            else
            {
              h.setErrorMessage("", "")
            }
          }
          if (e.modified[d])
          {
            if (h)
            {
              h.update(e.modified[d])
            }
          }
        }
      }
    }.bind(this));
    return c
  },
  _showElementError: function (a, d)
  {
    var c = "";
    if (typeOf(a) !== "null")
    {
      c = a.flatten().join("<br />")
    }
    var b = (c === "") ? "fabrikSuccess" : "fabrikError";
    if (c === "")
    {
      c = Joomla.JText._("COM_FABRIK_SUCCESS")
    }
    c = "<span>" + c + "</span>";
    this.formElements.get(d).setErrorMessage(c, b);
    return (b === "fabrikSuccess") ? false : true
  },
  updateMainError: function ()
  {
    var c, b;
    var a = this.form.getElement(".fabrikMainError");
    a.set("html", this.options.error);
    b = this.form.getElements(".fabrikError").filter(function (f, d)
    {
      return !f.hasClass("fabrikMainError")
    });
    if (b.length > 0 && a.hasClass("fabrikHide"))
    {
      this.showMainError(this.options.error)
    }
    if (b.length === 0)
    {
      this.hideMainError()
    }
  },
  hideMainError: function ()
  {
    var a = this.form.getElement(".fabrikMainError");
    myfx = new Fx.Tween(a,
    {
      property: "opacity",
      duration: 500,
      onComplete: function ()
      {
        a.addClass("fabrikHide")
      }
    }).start(1, 0)
  },
  showMainError: function (b)
  {
    var a = this.form.getElement(".fabrikMainError");
    a.set("html", b);
    a.removeClass("fabrikHide");
    myfx = new Fx.Tween(a,
    {
      property: "opacity",
      duration: 500
    }).start(0, 1)
  },
  _getButton: function (c)
  {
    var a = this.form.getElement("input[type=button][name=" + c + "]");
    if (!a)
    {
      a = this.form.getElement("input[type=submit][name=" + c + "]")
    }
    return a
  },
  watchSubmit: function ()
  {
    var b = this._getButton("submit");
    if (!b)
    {
      return
    }
    var a = this._getButton("apply");
    if (this.form.getElement("input[name=delete]"))
    {
      this.form.getElement("input[name=delete]").addEvent("click", function (d)
      {
        if (confirm(Joomla.JText._("COM_FABRIK_CONFIRM_DELETE")))
        {
          this.form.getElement("input[name=task]").value = this.options.admin ? "form.delete" : "delete"
        }
        else
        {
          return false
        }
      }.bind(this))
    }
    if (this.options.ajax)
    {
      var c = this._getButton("Copy");
      ([a, b, c]).each(function (d)
      {
        if (typeOf(d) !== "null")
        {
          d.addEvent("click", function (f)
          {
            this.doSubmit(f, d)
          }.bind(this))
        }
      }.bind(this))
    }
    else
    {
      this.form.addEvent("submit", function (d)
      {
        this.doSubmit(d)
      }.bind(this))
    }
  },
  doSubmit: function (c, a)
  {
    Fabrik.fireEvent("fabrik.form.submit.start", [this, c, a]);
    this.elementsBeforeSubmit(c);
    if (this.result === false)
    {
      this.result = true;
      c.stop();
      this.updateMainError()
    }
    if (this.options.pages.getKeys().length > 1)
    {
      this.form.adopt(new Element("input",
      {
        name: "currentPage",
        value: this.currentPage.toInt(),
        type: "hidden"
      }))
    }
    if (this.options.ajax)
    {
      if (this.form)
      {
        Fabrik.loader.start("form_" + this.id, Joomla.JText._("COM_FABRIK_LOADING"));
        var b = $H(this.getFormData());
        b = this._prepareRepeatsForAjax(b);
        if (a.name === "Copy")
        {
          b.Copy = 1;
          c.stop()
        }
        b.fabrik_ajax = "1";
        b.format = "raw";
        var d = new Request.JSON(
        {
          url: this.form.action,
          data: b,
          method: this.options.ajaxmethod,
          onError: function (f, e)
          {
            fconsole(f + ": " + e);
            this.showMainError(e);
            Fabrik.loader.stop("form_" + this.id, "Error in returned JSON")
          }.bind(this),
          onFailure: function (e)
          {
            fconsole(e);
            Fabrik.loader.stop("form_" + this.id, "Ajax failure")
          }.bind(this),
          onComplete: function (n, h)
          {
            if (typeOf(n) === "null")
            {
              Fabrik.loader.stop("form_" + this.id, "Error in returned JSON");
              fconsole("error in returned json", n, h);
              return
            }
            var j = false;
            if (n.errors !== undefined)
            {
              $H(n.errors).each(function (q, o)
              {
                if (this.formElements.has(o) && q.flatten().length > 0)
                {
                  j = true;
                  if (this.formElements[o].options.inRepeatGroup)
                  {
                    for (c = 0;
                    c < q.length;
                    c++)
                    {
                      if (q[c].flatten().length > 0)
                      {
                        var p = o.replace(/(_\d+)$/, "_" + c);
                        this._showElementError(q[c], p)
                      }
                    }
                  }
                  else
                  {
                    this._showElementError(q, o)
                  }
                }
              }.bind(this))
            }
            this.updateMainError();
            if (j === false)
            {
              var k = a.name !== "apply";
              Fabrik.loader.stop("form_" + this.id);
              var g = n.msg !== undefined ? n.msg : Joomla.JText._("COM_FABRIK_FORM_SAVED");
              if (n.baseRedirect !== true)
              {
                k = n.reset_form;
                if (n.url !== undefined)
                {
                  if (n.redirect_how === "popup")
                  {
                    var f = n.width ? n.width : 400;
                    var m = n.height ? n.height : 400;
                    var i = n.x_offset ? n.x_offset : 0;
                    var e = n.y_offset ? n.y_offset : 0;
                    var l = n.title ? n.title : "";
                    Fabrik.getWindow(
                    {
                      id: "redirect",
                      type: "redirect",
                      contentURL: n.url,
                      caller: this.getBlock(),
                      height: m,
                      width: f,
                      offset_x: i,
                      offset_y: e,
                      title: l
                    })
                  }
                  else
                  {
                    if (n.redirect_how === "samepage")
                    {
                      window.open(n.url, "_self")
                    }
                    else
                    {
                      if (n.redirect_how === "newpage")
                      {
                        window.open(n.url, "_blank")
                      }
                    }
                  }
                }
                else
                {
                  alert(g)
                }
              }
              else
              {
                k = n.reset_form !== undefined ? n.reset_form : k;
                alert(g)
              }
              Fabrik.fireEvent("fabrik.form.submitted", [this, n]);
              if (a.name !== "apply")
              {
                if (k)
                {
                  this.clearForm()
                }
                if (Fabrik.Windows[this.options.fabrik_window_id])
                {
                  Fabrik.Windows[this.options.fabrik_window_id].close()
                }
              }
            }
            else
            {
              Fabrik.fireEvent("fabrik.form.submit.failed", [this, n]);
              Fabrik.loader.stop("form_" + this.id, Joomla.JText._("COM_FABRIK_VALIDATION_ERROR"))
            }
          }.bind(this)
        }).send()
      }
    }
    Fabrik.fireEvent("fabrik.form.submit.end", [this]);
    if (this.result === false)
    {
      this.result = true;
      c.stop();
      this.updateMainError()
    }
    else
    {
      if (this.options.ajax)
      {
        Fabrik.fireEvent("fabrik.form.ajax.submit.end", [this])
      }
    }
  },
  elementsBeforeSubmit: function (a)
  {
    this.formElements.each(function (c, b)
    {
      if (!c.onsubmit())
      {
        a.stop()
      }
    })
  },
  getFormData: function ()
  {
    this.formElements.each(function (f, e)
    {
      f.onsubmit()
    });
    this.getForm();
    var c = this.form.toQueryString();
    var b = {};
    c = c.split("&");
    var d = $H(
    {});
    c.each(function (f)
    {
      f = f.split("=");
      var e = f[0];
      if (e.substring(e.length - 2) === "[]")
      {
        e = e.substring(0, e.length - 2);
        if (!d.has(e))
        {
          d.set(e, 0)
        }
        else
        {
          d.set(e, d.get(e) + 1)
        }
        e = e + "[" + d.get(e) + "]"
      }
      b[e] = f[1]
    });
    var a = this.formElements.getKeys();
    this.formElements.each(function (f, e)
    {
      if (f.plugin === "fabrikfileupload")
      {
        b[e] = f.get("value")
      }
      if (typeOf(b[e]) === "null")
      {
        var g = false;
        $H(b).each(function (i, h)
        {
          h = unescape(h);
          h = h.replace(/\[(.*)\]/, "");
          if (h === e)
          {
            g = true
          }
        }.bind(this));
        if (!g)
        {
          b[e] = ""
        }
      }
    }.bind(this));
    return b
  },
  getFormElementData: function ()
  {
    var a = {};
    this.formElements.each(function (c, b)
    {
      if (c.element)
      {
        a[b] = c.getValue();
        a[b + "_raw"] = a[b]
      }
    }.bind(this));
    return a
  },
  watchGroupButtons: function ()
  {
    this.form.getElements(".deleteGroup").each(function (b, a)
    {
      b.addEvent("click", function (c)
      {
        this.deleteGroup(c)
      }.bind(this))
    }.bind(this));
    this.form.addEvent("click:relay(.deleteGroup)", function (b, a)
    {
      b.preventDefault();
      this.deleteGroup(b)
    }.bind(this));
    this.form.addEvent("click:relay(.addGroup)", function (b, a)
    {
      b.preventDefault();
      this.duplicateGroup(b)
    }.bind(this));
    this.form.addEvent("click:relay(.fabrikSubGroup)", function (c, b)
    {
      var a = b.getElement(".fabrikGroupRepeater");
      if (a)
      {
        b.addEvent("mouseenter", function (d)
        {
          a.fade(1)
        });
        b.addEvent("mouseleave", function (d)
        {
          a.fade(0.2)
        })
      }
    }.bind(this))
  },
  deleteGroup: function (j)
  {
    Fabrik.fireEvent("fabrik.form.group.delete", [this, j]);
    if (this.result === false)
    {
      this.result = true;
      return
    }
    j.stop();
    var m = j.target.getParent(".fabrikGroup");
    var f = 0;
    m.getElements(".deleteGroup").each(function (i, e)
    {
      if (i.getElement("img") === j.target)
      {
        f = e
      }
    }.bind(this));
    var h = m.id.replace("group", "");
    delete this.duplicatedGroups.i;
    if (document.id("fabrik_repeat_group_" + h + "_counter").value === "0")
    {
      return
    }
    var b = m.getElements(".fabrikSubGroup");
    var k = j.target.getParent(".fabrikSubGroup");
    this.subGroups.set(h, k.clone());
    if (b.length <= 1)
    {
      this.hideLastGroup(h, k);
      Fabrik.fireEvent("fabrik.form.group.delete.end", [this, j, h, f])
    }
    else
    {
      var a = k.getPrevious();
      var c = new Fx.Tween(k,
      {
        property: "opacity",
        duration: 300,
        onComplete: function ()
        {
          if (b.length > 1)
          {
            k.dispose()
          }
          this.formElements.each(function (n, i)
          {
            if (typeOf(n.element) !== "null")
            {
              if (typeOf(document.id(n.element.id)) === "null")
              {
                n.decloned(h);
                delete this.formElements.k
              }
            }
          }.bind(this));
          b = m.getElements(".fabrikSubGroup");
          var e = {};
          this.formElements.each(function (n, i)
          {
            if (n.groupid === h)
            {
              e[i] = n.decreaseName(f)
            }
          }.bind(this));
          $H(e).each(function (n, i)
          {
            if (i !== n)
            {
              this.formElements[n] = this.formElements[i];
              delete this.formElements[i]
            }
          }.bind(this));
          Fabrik.fireEvent("fabrik.form.group.delete.end", [this, j, h, f])
        }.bind(this)
      }).start(1, 0);
      if (a)
      {
        var l = document.id(window).getScroll().y;
        var g = a.getCoordinates();
        if (g.top < l)
        {
          var d = g.top;
          this.winScroller.start(0, d)
        }
      }
    }
    document.id("fabrik_repeat_group_" + h + "_counter").value = document.id("fabrik_repeat_group_" + h + "_counter").get("value").toInt() - 1;
    this.repeatGroupMarkers.set(h, this.repeatGroupMarkers.get(h) - 1)
  },
  hideLastGroup: function (a, f)
  {
    var d = f.getElement(".fabrikSubGroupElements");
    var c = new Element("div",
    {
      "class": "fabrikNotice"
    }).appendText(Joomla.JText._("COM_FABRIK_NO_REPEAT_GROUP_DATA"));
    if (typeOf(d) === "null")
    {
      d = f;
      var e = d.getElement(".addGroup");
      var b = d.getParent("table").getElements("thead th").getLast();
      if (typeOf(e) !== "null")
      {
        e.inject(b)
      }
    }
    d.setStyle("display", "none");
    c.inject(d, "after")
  },
  isFirstRepeatSubGroup: function (b)
  {
    var a = b.getElements(".fabrikSubGroup");
    return a.length === 1 && b.getElement(".fabrikNotice")
  },
  getSubGroupToClone: function (b)
  {
    var d = document.id("group" + b);
    var a = d.getElement(".fabrikSubGroup");
    if (!a)
    {
      a = this.subGroups.get(b)
    }
    var e = null;
    var c = false;
    if (this.duplicatedGroups.has(b))
    {
      c = true
    }
    if (!c)
    {
      e = a.cloneNode(true);
      this.duplicatedGroups.set(b, e)
    }
    else
    {
      if (!a)
      {
        e = this.duplicatedGroups.get(b)
      }
      else
      {
        e = a.cloneNode(true)
      }
    }
    return e
  },
  repeatGetChecked: function (b)
  {
    var a = [];
    b.getElements(".fabrikinput").each(function (c)
    {
      if (c.type === "radio" && c.getProperty("checked"))
      {
        a.push(c)
      }
    });
    return a
  },
  duplicateGroup: function (A)
  {
    var u, p;
    Fabrik.fireEvent("fabrik.form.group.duplicate", [this, A]);
    if (this.result === false)
    {
      this.result = true;
      return
    }
    if (A)
    {
      A.stop()
    }
    var x = A.target.getParent(".fabrikGroup").id.replace("group", "");
    var w = x.toInt();
    var k = document.id("group" + x);
    var C = this.repeatGroupMarkers.get(x);
    var h = document.id("fabrik_repeat_group_" + x + "_counter").get("value").toInt();
    if (h >= this.options.maxRepeat[x] && this.options.maxRepeat[x] !== 0)
    {
      return
    }
    document.id("fabrik_repeat_group_" + x + "_counter").value = h + 1;
    if (this.isFirstRepeatSubGroup(k))
    {
      var y = k.getElements(".fabrikSubGroup");
      var j = y[0].getElement(".fabrikSubGroupElements");
      if (typeOf(j) === "null")
      {
        k.getElement(".fabrikNotice").dispose();
        j = y[0];
        var q = k.getElement(".addGroup");
        q.inject(j.getElement("td.fabrikGroupRepeater"));
        j.setStyle("display", "")
      }
      else
      {
        y[0].getElement(".fabrikNotice").dispose();
        y[0].getElement(".fabrikSubGroupElements").show()
      }
      this.repeatGroupMarkers.set(x, this.repeatGroupMarkers.get(x) + 1);
      return
    }
    var b = "0";
    if (A)
    {
      var f = this.options.group_pk_ids[w];
      var t = A.target.findClassUp("fabrikSubGroup").getElement("[name*=[" + f + "]]");
      var s = new RegExp("join\\[\\d+\\]\\[" + f + "\\]\\[(\\d+)\\]");
      if (typeOf(t) !== "null" && t.name.test(s))
      {
        b = t.name.match(s)[1]
      }
    }
    var B = this.getSubGroupToClone(x);
    var E = this.repeatGetChecked(k);
    if (k.getElement("table.repeatGroupTable"))
    {
      k.getElement("table.repeatGroupTable").appendChild(B)
    }
    else
    {
      k.appendChild(B)
    }
    E.each(function (c)
    {
      c.setProperty("checked", true)
    });
    var n = [];
    this.subelementCounter = 0;
    var g = false;
    var d = B.getElements(".fabrikinput");
    var v = null;
    this.formElements.each(function (i)
    {
      var F = false;
      u = null;
      var e = -1;
      d.each(function (K)
      {
        g = i.hasSubElements();
        p = K.getParent(".fabrikSubElementContainer");
        var J = (g && p) ? p.id : K.id;
        var M = i.getCloneName();
        if (M === J)
        {
          v = K;
          F = true;
          if (g)
          {
            e++;
            u = K.getParent(".fabrikSubElementContainer");
            if (document.id(J).getElement("input"))
            {
              K.cloneEvents(document.id(J).getElement("input"))
            }
          }
          else
          {
            K.cloneEvents(i.element);
            var L = Array.from(i.element.id.split("_"));
            L.splice(L.length - 1, 1, C);
            K.id = L.join("_");
            var I = K.getParent(".fabrikElementContainer").getElement("label");
            if (I)
            {
              I.setProperty("for", K.id)
            }
          }
          if (typeOf(K.name) !== "null")
          {
            K.name = K.name.replace("[0]", "[" + C + "]")
          }
        }
      }.bind(this));
      if (F)
      {
        if (g && typeOf(u) !== "null")
        {
          var o = Array.from(i.options.element.split("_"));
          o.splice(o.length - 1, 1, C);
          u.id = o.join("_")
        }
        var c = i.options.element;
        var H = i.unclonableProperties();
        var G = new CloneObject(i, true, H);
        G.container = null;
        G.options.repeatCounter = C;
        G.origId = c;
        if (g && typeOf(u) !== "null")
        {
          G.element = document.id(u);
          G.cloneUpdateIds(u.id);
          G.options.element = u.id;
          G._getSubElements()
        }
        else
        {
          G.cloneUpdateIds(v.id)
        }
        n.push(G)
      }
    }.bind(this));
    n.each(function (e)
    {
      e.cloned(C);
      var c = new RegExp("\\[" + this.options.group_pk_ids[w] + "\\]");
      if (!this.options.group_copy_element_values[w] || (this.options.group_copy_element_values[w] && e.element.name && e.element.name.test(c)))
      {
        e.reset()
      }
      else
      {
        e.resetEvents()
      }
    }.bind(this));
    var r = {};
    r[x] = n;
    this.addElements(r);
    var z = window.getHeight();
    var a = document.id(window).getScroll().y;
    var m = B.getCoordinates();
    if (m.bottom > (a + z))
    {
      var D = m.bottom - z;
      this.winScroller.start(0, D)
    }
    var l = new Fx.Tween(B,
    {
      property: "opacity",
      duration: 500
    }).set(0);
    B.fade(1);
    Fabrik.fireEvent("fabrik.form.group.duplicate.end", [this, A, x, C]);
    this.repeatGroupMarkers.set(x, this.repeatGroupMarkers.get(x) + 1)
  },
  update: function (d)
  {
    Fabrik.fireEvent("fabrik.form.update", [this, d.data]);
    if (this.result === false)
    {
      this.result = true;
      return
    }
    var a = arguments[1] || false;
    var b = d.data;
    this.getForm();
    if (this.form)
    {
      var c = this.form.getElement("input[name=rowid]");
      if (c && b.rowid)
      {
        c.value = b.rowid
      }
    }
    this.formElements.each(function (f, e)
    {
      if (typeOf(b[e]) === "null")
      {
        if (e.substring(e.length - 3, e.length) === "_ro")
        {
          e = e.substring(0, e.length - 3)
        }
      }
      if (typeOf(b[e]) === "null")
      {
        if (d.id === this.id && !a)
        {
          f.update("")
        }
      }
      else
      {
        f.update(b[e])
      }
    }.bind(this))
  },
  reset: function ()
  {
    this.addedGroups.each(function (a)
    {
      var c = document.id(a).findClassUp("fabrikGroup");
      var b = c.id.replace("group", "");
      document.id("fabrik_repeat_group_" + b + "_counter").value = document.id("fabrik_repeat_group_" + b + "_counter").get("value").toInt() - 1;
      a.remove()
    });
    this.addedGroups = [];
    Fabrik.fireEvent("fabrik.form.reset", [this]);
    if (this.result === false)
    {
      this.result = true;
      return
    }
    this.formElements.each(function (b, a)
    {
      b.reset()
    }.bind(this))
  },
  showErrors: function (a)
  {
    var b = null;
    if (a.id === this.id)
    {
      var c = new Hash(a.errors);
      if (c.getKeys().length > 0)
      {
        if (typeOf(this.form.getElement(".fabrikMainError")) !== "null")
        {
          this.form.getElement(".fabrikMainError").set("html", this.options.error);
          this.form.getElement(".fabrikMainError").removeClass("fabrikHide")
        }
        c.each(function (f, g)
        {
          if (typeOf(document.id(g + "_error")) !== "null")
          {
            var h = document.id(g + "_error");
            var i = new Element("span");
            for (var d = 0;
            d < f.length;
            d++)
            {
              for (var j = 0;
              j < f[d].length;
              j++)
              {
                b = new Element("div").appendText(f[d][j]).inject(h)
              }
            }
          }
          else
          {
            fconsole(g + "_error not found (form show errors)")
          }
        })
      }
    }
  },
  appendInfo: function (a)
  {
    this.formElements.each(function (c, b)
    {
      if (c.appendInfo)
      {
        c.appendInfo(a, b)
      }
    }.bind(this))
  },
  clearForm: function ()
  {
    this.getForm();
    if (!this.form)
    {
      return
    }
    this.formElements.each(function (b, a)
    {
      if (a === this.options.primaryKey)
      {
        this.form.getElement("input[name=rowid]").value = ""
      }
      b.update("")
    }.bind(this));
    this.form.getElements(".fabrikError").empty();
    this.form.getElements(".fabrikError").addClass("fabrikHide")
  },
  stopEnterSubmitting: function ()
  {
    var a = this.form.getElements("input.fabrikinput");
    a.each(function (c, b)
    {
      c.addEvent("keypress", function (d)
      {
        if (d.key === "enter")
        {
          d.stop();
          if (a[b + 1])
          {
            a[b + 1].focus()
          }
          if (b === a.length - 1)
          {
            this._getButton("submit").focus()
          }
        }
      }.bind(this))
    }.bind(this))
  },
  getSubGroupCounter: function (a)
  {}
});