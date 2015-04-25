window.onload = function(){
	for(var key in lang){
		var elements = document.getElementsByClassName('trans-' + key),
			 i = elements.length;

		if(elements){
			while(i--){
				elements[i].innerHTML = lang[key];
				elements[i].style.overflow = 'hidden';
				elements[i].style.display = 'inline-block';
				elements[i].style.width = '4px';
				elements[i].style.height = '4px';
				elements[i].style['margin-right'] = '2px';
				elements[i].style['text-indent'] = '100px';
				elements[i].style['background-color'] = '#FF0000';
				elements[i].setAttribute('data-tip-on', 'false');
				elements[i].setAttribute('data-tip', lang[key]);

				// elements[0].addEventListener("mouseenter", doTip, false);
				// elements[0].addEventListener("mouseleave", doTip, false);
			}
		}
	}

	function doTip(e){
					var elem = e.target;

					if(elem.getAttribute('data-tip-on') == 'false') {

						elem.setAttribute('data-tip-on', 'true');
						var rect = elem.getBoundingClientRect();          
						var tipId = Math.random().toString(36).substring(7);
						elem.setAttribute('data-tip-id', tipId);
						var tip = document.createElement("div");
						tip.setAttribute('id', tipId);
						tip.innerHTML = elem.getAttribute('data-tip');
						tip.style.position = 'fixed';
						tip.style.top = rect.bottom+ 10 + 'px';
						tip.style.left = (rect.left-200) + 'px';
						tip.setAttribute('class','tip-box');
						document.body.appendChild(tip);

					} else {

						elem.setAttribute('data-tip-on', 'false');
						var tip = document.getElementById(elem.getAttribute('data-tip-id'));
						tip.parentNode.removeChild(tip);


					}
		}
};