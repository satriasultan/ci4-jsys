<div class="container-fluid">

    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold text-dark">Corporate Accounting Dashboard</h4>
        </div>
    </div>

    <!-- KPI ROW -->
    <div class="row g-4 mb-4">

        <div class="col-xl-2 col-md-4">
            <div class="card text-white bg-primary shadow-sm">
                <div class="card-body">
                    <small>Total Revenue</small>
                    <h5 class="fw-bold">$ 1,250,000</h5>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4">
            <div class="card text-white bg-danger shadow-sm">
                <div class="card-body">
                    <small>Total Expense</small>
                    <h5 class="fw-bold">$ 820,000</h5>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4">
            <div class="card text-white bg-success shadow-sm">
                <div class="card-body">
                    <small>Net Profit</small>
                    <h5 class="fw-bold">$ 430,000</h5>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4">
            <div class="card text-white bg-warning shadow-sm">
                <div class="card-body">
                    <small>Accounts Receivable</small>
                    <h5 class="fw-bold">$ 540,000</h5>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4">
            <div class="card text-white bg-info shadow-sm">
                <div class="card-body">
                    <small>Accounts Payable</small>
                    <h5 class="fw-bold">$ 310,000</h5>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4">
            <div class="card text-white bg-secondary shadow-sm">
                <div class="card-body">
                    <small>Total Assets</small>
                    <h5 class="fw-bold">$ 3,200,000</h5>
                </div>
            </div>
        </div>

    </div>

    <!-- CHART ROW -->
    <div class="row g-4">

        <!-- PIE -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Expense Distribution</h6>
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>

        <!-- BAR GRADIENT -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Monthly Revenue</h6>
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>

        <!-- STACKED -->
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Quarter Revenue vs Expense</h6>
                    <canvas id="stackedChart"></canvas>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
    /* PIE */
    new Chart(document.getElementById('pieChart'), {
        type:'pie',
        data:{
            labels:['Salary','Operational','Marketing','IT','Tax','Other'],
            datasets:[{
                data:[300000,200000,150000,100000,50000,20000],
                backgroundColor:[
                    '#0d6efd','#dc3545','#198754','#ffc107','#6f42c1','#20c997'
                ]
            }]
        },
        options:{plugins:{legend:{position:'bottom'}}}
    });


    /* BAR WITH GRADIENT */
    const ctxBar = document.getElementById('barChart').getContext('2d');
    const gradient = ctxBar.createLinearGradient(0,0,0,400);
    gradient.addColorStop(0,'#4e73df');
    gradient.addColorStop(1,'#1e3a8a');

    new Chart(ctxBar,{
        type:'bar',
        data:{
            labels:['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug'],
            datasets:[{
                label:'Revenue',
                data:[180000,210000,250000,220000,260000,290000,310000,330000],
                backgroundColor:gradient,
                borderRadius:6
            }]
        },
        options:{
            plugins:{legend:{display:false}},
            scales:{
                y:{beginAtZero:true},
                x:{grid:{display:false}}
            }
        }
    });


    /* STACKED */
    new Chart(document.getElementById('stackedChart'),{
        type:'bar',
        data:{
            labels:['Q1','Q2','Q3','Q4'],
            datasets:[
                {
                    label:'Revenue',
                    data:[600000,750000,820000,900000],
                    backgroundColor:'#0d6efd'
                },
                {
                    label:'Expense',
                    data:[450000,520000,580000,610000],
                    backgroundColor:'#dc3545'
                }
            ]
        },
        options:{
            scales:{
                x:{stacked:true},
                y:{stacked:true}
            }
        }
    });
</script>