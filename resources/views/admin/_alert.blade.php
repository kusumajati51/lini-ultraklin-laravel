@if (session()->has('success'))
    <div role="alert" class="el-alert el-alert--success uk-margin-bottom">
        <i class="el-alert__icon el-icon-success is-big"></i>
        <div class="el-alert__content">
            <span class="el-alert__title is-bold">Success</span>
            <p class="el-alert__description">{{ session('success') }}</p>
        </div>
    </div>
@endif
@if (session()->has('error'))
    <div role="alert" class="el-alert el-alert--error uk-margin-bottom">
        <i class="el-alert__icon el-icon-error is-big"></i>
        <div class="el-alert__content">
            <span class="el-alert__title is-bold">Error</span>
            <p class="el-alert__description">{{ session('error') }}</p>
        </div>
    </div>
@endif