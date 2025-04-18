<div>
    <div class="container">
        <div class="row my-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Today's Sales</h5>
                        <p class="card-text">{{ $todaySales }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Today's Revenue</h5>
                        <p class="card-text">Rp {{ number_format($todayRevenue, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">Total Sales</h5>
                        <p class="card-text">{{ $totalSales }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Total Revenue</h5>
                        <p class="card-text">Rp {{ number_format($totalRevenue, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Livewire.on('loginSuccess', (message) => {
            Swal.fire({
                title: "Success!",
                text: message,
                icon: "success",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK"
            });
        });

        Livewire.on('logoutSuccess', (message) => {
            Swal.fire({
                title: "Logged Out!",
                text: message,
                icon: "info",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK"
            });
        });
    });
</script>
