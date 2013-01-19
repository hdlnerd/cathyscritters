window.addEvent('domready', function() {
	$$('#menu ul li.parent').addEvent('mouseenter', function() {
		var x = this.getPosition().x;
		var wp = 0;
		try {
			wp = this.getSize().size.x;
		}
		catch (e) {
			wp = this.getSize().x;
		}
		var li = this;
		var sm = this.getElement('ul');
		if (sm) {
			sm.setStyle('display','block');
			var w = 0;
			var mw = 0;
			try {
				w = sm.getSize().size.x;
				mw = window.getSize().size.x;
			}
			catch (e) {
				w = sm.getSize().x;
				mw = window.getSize().x;
			}
			var posx = sm.getPosition().x;
			var par = li.getParent('li.parent');
			if (par) {
				if (posx + w > mw) {
					sm.setStyle('left','-' + (w) + 'px');
				}
			}
			else {
				if (x+w > mw) {
					sm.setStyle('left','-' + (w-wp) + 'px');
				}
			}
		}
	}).addEvent('mouseleave', function() {
		var sm = this.getElement('ul');
		if (sm) {
			sm.setStyle('display','none');
		}
	})
});
