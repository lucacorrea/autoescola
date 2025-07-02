// SIDEBAR TOGGLE

let sidebarOpen = false;
const sidebar = document.getElementById('sidebar');

function openSidebar() {
  if (!sidebarOpen) {
    sidebar.classList.add('sidebar-responsive');
    sidebarOpen = true;
  }
}

function closeSidebar() {
  if (sidebarOpen) {
    sidebar.classList.remove('sidebar-responsive');
    sidebarOpen = false;
  }
}

// ---------- CHARTS ----------

// BAR CHART
const barChartOptions = {
  series: [
    {
      data: [50, 26, 25],
      name: 'Total',
    },
  ],
  chart: {
    type: 'bar',
    background: 'transparent',
    height: 230,
    toolbar: {
      show: false,
    },
  },
  colors: ['#2962ff', '#d50000', '#ff6d00'],
  plotOptions: {
    bar: {
      distributed: true,
      borderRadius: 4,
      horizontal: false,
      columnWidth: '40%',
    },
  },
  dataLabels: {
    enabled: false,
  },
  fill: {
    opacity: 1,
  },
  grid: {
    borderColor: '#ccc',
    yaxis: {
      lines: {
        show: true,
      },
    },
    xaxis: {
      lines: {
        show: true,
      },
    },
  },
  legend: {
    labels: {
      colors: '#333',
    },
    show: true,
    position: 'top',
  },
  stroke: {
    colors: ['transparent'],
    show: true,
    width: 2,
  },
  tooltip: {
    shared: true,
    intersect: false,
    theme: 'dark',
  },
  xaxis: {
    categories: ['Instrutores', 'Cursos', 'Veículos'],
    title: {
      style: {
        color: '#fff',
      },
    },
    axisBorder: {
      show: true,
      color: '#ccc',
    },
    axisTicks: {
      show: true,
      color: '#ccc',
    },
    labels: {
      style: {
        colors: '#333',
      },
    },
  },
  yaxis: {
    title: {
      text: 'Count',
      style: {
        color: '#333',
      },
    },
    axisBorder: {
      color: '#ccc',
      show: true,
    },
    axisTicks: {
      color: '#ccc',
      show: true,
    },
    labels: {
      style: {
        colors: '#333',
      },
    },
  },
};

const barChart = new ApexCharts(
  document.querySelector('#bar-chart'),
  barChartOptions
);
barChart.render();

// AREA CHART
const areaChartOptions = {
  series: [
    {
      name: 'Alunos Aprovados',
      data: [31, 40, 28, 51, 42, 109],
    },
    {
      name: 'Alunos Reprovados',
      data: [11, 32, 45, 32, 34, 52],
    },
  ],
  chart: {
    type: 'area',
    background: 'transparent',
    height: 230,
    stacked: false,
    toolbar: {
      show: false,
    },
  },
  colors: ['#00ab57', '#d50000'],
  labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'],
  dataLabels: {
    enabled: false,
  },
  fill: {
    gradient: {
      opacityFrom: 0.4,
      opacityTo: 0.1,
      shadeIntensity: 1,
      stops: [0, 100],
      type: 'vertical',
    },
    type: 'gradient',
  },
  grid: {
    borderColor: '#ccc',
    yaxis: {
      lines: {
        show: true,
      },
    },
    xaxis: {
      lines: {
        show: true,
      },
    },
  },
  legend: {
    labels: {
      colors: '#333',
    },
    show: true,
    position: 'top',
  },
  markers: {
    size: 6,
    strokeColors: '#333',
    strokeWidth: 3,
  },
  stroke: {
    curve: 'smooth',
  },
  xaxis: {
    axisBorder: {
      color: '#ccc',
      show: true,
    },
    axisTicks: {
      color: '#ccc',
      show: true,
    },
    labels: {
      offsetY: 5,
      style: {
        colors: '#333',
      },
    },
  },
  yaxis: [
    {
      title: {
        text: 'Purchase Orders',
        style: {
          color: '#333',
        },
      },
      labels: {
        style: {
          colors: ['#333'],
        },
      },
    },
    {
      opposite: true,
      title: {
        text: 'Sales Orders',
        style: {
          color: '#333',
        },
      },
      labels: {
        style: {
          colors: ['#333'],
        },
      },
    },
  ],
  tooltip: {
    shared: true,
    intersect: false,
    theme: 'dark',
  },
};

const areaChart = new ApexCharts(
  document.querySelector('#area-chart'),
  areaChartOptions
);
areaChart.render();
