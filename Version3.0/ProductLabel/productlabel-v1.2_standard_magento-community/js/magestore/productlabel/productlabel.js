    
function setLabelPosition(element, position){
        var eH = element.offsetHeight;
        var pH = element.parentNode.offsetHeight;
        var eT = 0;
        var eW = element.offsetWidth;
        var pW = element.parentNode.offsetWidth;
        var eL = 0;
				switch(position){
					case 1:
						eL = 0;
						eT = 0;
						break;
					case 2:
						eL = (pW - eW)/2; 
						eT = 0;
						break;
					case 3:
						eL = pW - eW;
						eT = 0;
						break;
					case 4:
						eL = 0;
						eT = (pH - eH)/2;
						break;
					case 5:
						eL = (pW - eW)/2; 
						eT = (pH - eH)/2; 
						break;
					case 6:
						eL = pW - eW; 
						eT = (pH - eH)/2; 
						break;
					case 7:
						eL = 0;
						eT = pH - eH;
						break;
					case 8:
						eL = (pW - eW)/2; 
						eT = pH - eH; 
						break;
					case 9:
						eL = pW - eW; 
						eT = pH - eH; 
						break;
				}
		element.style.left = eL + 'px';
        element.style.right = null;
		element.style.top = eT + 'px';
        element.style.bottom = null;
}