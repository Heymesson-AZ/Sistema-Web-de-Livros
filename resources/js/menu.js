import $ from 'jquery';

$(window).on('load', function () {
	// --- 1. CONFIGURAÇÕES E VARIÁVEIS INICIAIS ---
	var height = window.innerHeight,
		x = 0, y = height / 2,          // Posição atual do mouse
		curveX = 10, curveY = 0,        // Posição da curva do SVG
		targetX = 0,                    // Onde a curva quer chegar
		xitteration = 0, yitteration = 0,
		menuExpanded = false;

	let blob = $('#blob');              // O container do SVG
	let blobPath = $('#blob-path');     // O desenho (caminho) do SVG
	let hamburger = $('.hamburger');    // O botão de três linhas

	// --- 2. RASTREAMENTO DO MOUSE ---
	// Atualiza constantemente as coordenadas X e Y conforme o mouse se move
	$(this).on('mousemove', function (e) {
		x = e.pageX;
		y = e.pageY;
	});

	// --- 3. CONTROLE DE EXPANSÃO DO MENU ---
	// Quando o mouse entra no botão ou no menu, ele "estica"
	$('.hamburger, .menu-inner').on('mouseenter', function () {
		$(this).parent().addClass('expanded');
		menuExpanded = true;
	});

	// Quando o mouse sai da área interna do menu, ele recolhe
	$('.menu-inner').on('mouseleave', function () {
		menuExpanded = false;
		$(this).parent().removeClass('expanded');
	});

	// Função matemática para suavizar o movimento (animação fluida)
	function easeOutExpo(currentIteration, startValue, changeInValue, totalIterations) {
		return changeInValue * (-Math.pow(2, -10 * currentIteration / totalIterations) + 1) + startValue;
	}

	// --- 4. ANIMAÇÃO DO SVG (O EFEITO "BLOB") ---
	var hoverZone = 150;        // Distância da borda que ativa o efeito
	var expandAmount = 20;      // O quanto a curva "empurra" para fora

	function svgCurve() {
		// Lógica para decidir se a curva deve seguir o mouse ou voltar ao repouso
		if ((curveX > x - 1) && (curveX < x + 1)) {
			xitteration = 0;
		} else {
			if (menuExpanded) {
				targetX = 0;    // Se o menu já está aberto, a curva fica reta
			} else {
				xitteration = 0;
				if (x > hoverZone) {
					targetX = 0;
				} else {
					// Calcula a força da curvatura baseada na proximidade do mouse
					targetX = -(((60 + expandAmount) / 100) * (x - hoverZone));
				}
			}
			xitteration++;
		}

		yitteration = 0;
		yitteration++;

		// Aplica a suavização nas coordenadas X e Y
		curveX = easeOutExpo(xitteration, curveX, targetX - curveX, 100);
		curveY = easeOutExpo(yitteration, curveY, y - curveY, 100);

		var anchorDistance = 200;
		var curviness = anchorDistance - 40;

		// Reconstrói o caminho (path) do SVG para criar a forma orgânica
		var newCurve2 = "M60," + height + "H0V0h60v" + (curveY - anchorDistance) +
			"c0," + curviness + "," + curveX + "," + curviness + "," +
			curveX + "," + anchorDistance + "S60," + (curveY) + ",60," +
			(curveY + (anchorDistance * 2)) + "V" + height + "z";

		blobPath.attr('d', newCurve2);
		blob.width(curveX + 60);

		// Faz o ícone do hambúrguer seguir a "ponta" da curva
		hamburger.css('transform', 'translate(' + curveX + 'px, ' + curveY + 'px)');

		// Executa a função novamente no próximo frame (animação contínua)
		window.requestAnimationFrame(svgCurve);
	}

	window.requestAnimationFrame(svgCurve);

	// --- 5. LÓGICA DOS SUBMENUS (CLIQUE) ---
	// Gerencia a abertura/fechamento das Categorias e Conta
	$('.menu-inner ul li:has(ul)').on('click', function (e) {
		// Evita que o clique feche o menu principal ou dispare outros eventos
		e.stopPropagation();

		// Fecha outros submenus abertos para manter o visual limpo
		$('.menu-inner ul li').not(this).removeClass('open');

		// Abre/Fecha o submenu atual
		$(this).toggleClass('open');
	});

	// Garante que os ícones do Lucide sejam renderizados
	if (typeof lucide !== 'undefined') {
		lucide.createIcons();
	}
});