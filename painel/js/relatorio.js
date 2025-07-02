document.addEventListener("DOMContentLoaded", function (event) {
    relatorio.event.init();
});

var relatorio = {};

var GRAFICO;
var LINHAS = ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago"];
var VALORES = [1110, 1220, 1340, 1450, 1320, 1220, 1390, 1560];

var PEDIDOS = [
    { mes: "Jan", quantidade: 10, valor: 1110 },
    { mes: "Fev", quantidade: 15, valor: 1220 },
    { mes: "Mar", quantidade: 20, valor: 1340 },
    { mes: "Abr", quantidade: 25, valor: 1450 },
    { mes: "Mai", quantidade: 18, valor: 1320 },
    { mes: "Jun", quantidade: 14, valor: 1220 },
    { mes: "Jul", quantidade: 22, valor: 1390 },
    { mes: "Ago", quantidade: 30, valor: 1560 },
];

relatorio.event = {
    init: () => {
        relatorio.method.iniciarGrafico();
        relatorio.method.listarPedidos();
        relatorio.method.exibirRelatorio();  // Chama a função para exibir o relatório
    }
}

relatorio.method = {

    iniciarGrafico: () => {
        const ctx = document.getElementById('graficoFaturamento').getContext("2d");

        GRAFICO = new Chart(ctx, {
            type: "line",
            data: {
                labels: LINHAS,
                datasets: [
                    {
                        label: "Faturamento",
                        data: VALORES,
                        borderWidth: 6,
                        fill: true,
                        backgroundColor: '#f8f8bb',
                        borderColor: '#ffbf00',
                        pointBackgroundColor: '#ffbf00',
                        pointRadius: 5,
                        pointHoverRadius: 5,
                        pointHitDetectionRadius: 35,
                        pointBorderWidth: 2.5,
                    },
                ],
            },
            options: {
                legend: { display: false },
                tooltips: {
                    enabled: false,
                    custom: function (tooltipModel) {
                        var tooltipEl = document.getElementById('chartjs-tooltip');
                        if (!tooltipEl) {
                            tooltipEl = document.createElement('div');
                            tooltipEl.id = 'chartjs-tooltip';
                            tooltipEl.innerHTML = '<table></table>';
                            document.body.appendChild(tooltipEl);
                        }
                        if (tooltipModel.opacity === 0) {
                            tooltipEl.style.opacity = 0;
                            return;
                        }
                        tooltipEl.classList.remove('above', 'below', 'no-transform');
                        if (tooltipModel.yAlign) {
                            tooltipEl.classList.add(tooltipModel.yAlign);
                        } else {
                            tooltipEl.classList.add('no-transform');
                        }

                        function getBody(bodyItem) {
                            return bodyItem.lines;
                        }

                        if (tooltipModel.body) {
                            var titleLines = tooltipModel.title || [];
                            var bodyLines = tooltipModel.body.map(getBody);
                            var innerHtml = '<thead>';

                            titleLines.forEach(function (title) {
                                innerHtml += '<tr><th>' + title + '</th></tr>';
                            });
                            innerHtml += '</thead><tbody>';

                            bodyLines.forEach(function (body, i) {
                                let valor = body[0].split(':')[1].trim();
                                let texto = body[0].split(':')[0].trim();

                                let formatado = texto + ': <b>R$ ' + valor + '</b>';

                                innerHtml += '<tr><td>' + formatado + '</td></tr>';
                                innerHtml += '<tr><td>Nº Pedidos: ' + '<b>' + PEDIDOS[i].quantidade + '</b>' + '</td></tr>';
                            });
                            innerHtml += '</tbody>';

                            var tableRoot = tooltipEl.querySelector('table');
                            tableRoot.innerHTML = innerHtml;
                        }

                        var position = this._chart.canvas.getBoundingClientRect();

                        tooltipEl.style.opacity = 1;
                        tooltipEl.style.position = 'absolute';
                        tooltipEl.style.left = position.left + window.pageXOffset + tooltipModel.caretX + 'px';
                        tooltipEl.style.top = position.top + window.pageYOffset + tooltipModel.caretY + 'px';
                        tooltipEl.style.fontFamily = tooltipModel._bodyFontFamily;
                        tooltipEl.style.fontSize = tooltipModel.bodyFontSize + 'px';
                        tooltipEl.style.fontStyle = tooltipModel._bodyFontStyle;
                        tooltipEl.style.padding = tooltipModel.yPadding + 'px ' + tooltipModel.xPadding + 'px';
                        tooltipEl.style.pointerEvents = 'none';
                    }
                },
                scales: {
                    yAxes: [
                        {
                            ticks: {
                                beginAtZero: false,
                                fontColor: '#999999',
                                fontSize: 10,
                                callback: (value, index, values) => {
                                    if (parseInt(value) >= 1000) {
                                        return 'R$' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                    } else {
                                        return 'R$' + value;
                                    }
                                }
                            },
                            gridLines: {
                                display: false,
                                drawBorder: false
                            }
                        },
                    ],
                    xAxes: [
                        {
                            ticks: {
                                fontColor: '#999999',
                                fontSize: 10
                            },
                            gridLines: {
                                display: false,
                                drawBorder: false
                            }
                        }
                    ]
                },
            },
        });
    },

    exibirRelatorio: () => {
        const relatorioContainer = document.getElementById('relatorioContainer');
        let relatorioHTML = `<table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Mês</th>
                                        <th>Quantidade de Pedidos</th>
                                        <th>Valor Total</th>
                                    </tr>
                                </thead>
                                <tbody>`;

        PEDIDOS.forEach((pedido) => {
            relatorioHTML += `<tr>
                                <td>${pedido.mes}</td>
                                <td>${pedido.quantidade}</td>
                                <td>R$ ${pedido.valor}</td>
                              </tr>`;
        });

        relatorioHTML += `</tbody>
                        </table>`;

        relatorioContainer.innerHTML = relatorioHTML;
    }

}
