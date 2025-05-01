<style>
    /* Dashboard-specific styles */
    .admin-dashboard-container {
        padding: 20px;
        transition: all 0.3s ease;
        max-width: 1200px;
        /* Reduced from 1400px */
        margin: 0 auto;
    }

    .admin-dashboard-header {
        margin-bottom: 20px;
    }

    .admin-dashboard-header h1 {
        color: #2C3E50;
        font-size: 22px;
        /* Slightly smaller */
        margin-bottom: 5px;
        font-weight: 500;
    }

    .admin-dashboard-header p {
        color: #7b8a8b;
        font-size: 13px;
        /* Smaller */
    }

    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        /* Smaller cards */
        gap: 12px;
        /* Smaller gap */
        margin-bottom: 20px;
    }

    .stat-card {
        border-radius: 6px;
        padding: 14px;
        /* Smaller padding */
        color: white;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
    }

    .stat-card-blue {
        background: linear-gradient(135deg, #4e73df, #224abe);
    }

    .stat-card-green {
        background: linear-gradient(135deg, #1cc88a, #13855c);
    }

    .stat-card-yellow {
        background: linear-gradient(135deg, #f6c23e, #dda20a);
    }

    .stat-card-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stat-card-text h3 {
        font-size: 12px;
        /* Smaller */
        font-weight: 500;
        text-transform: uppercase;
        margin: 0;
        opacity: 0.9;
        letter-spacing: 0.5px;
    }

    .stat-card-text p {
        font-size: 20px;
        /* Smaller */
        font-weight: 600;
        margin: 6px 0 0;
        /* Smaller */
    }

    .stat-card-icon {
        font-size: 24px;
        /* Smaller */
        opacity: 0.5;
    }

    .charts-container {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        /* Adjusted ratio */
        gap: 12px;
        /* Smaller gap */
        margin-top: 15px;
        /* Smaller */
        height: 260px;
        /* Reduced height from 300px */
    }

    .chart-card {
        background-color: white;
        border-radius: 6px;
        padding: 14px;
        /* Smaller padding */
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        /* Ensure nothing spills out */
    }

    .chart-header {
        border-bottom: 1px solid #edf2f7;
        padding-bottom: 8px;
        /* Smaller */
        margin-bottom: 12px;
        /* Smaller */
    }

    .chart-header h4 {
        margin: 0;
        color: #2d3748;
        font-size: 14px;
        /* Smaller */
        font-weight: 500;
    }

    .chart-body {
        height: 210px;
        /* Reduced from 250px */
    }

    /* Responsive adjustments for smaller screens */
    @media (max-width: 992px) {
        .charts-container {
            grid-template-columns: 1fr;
            height: auto;
        }

        .chart-card {
            margin-bottom: 15px;
            height: 240px;
            /* Fixed height for mobile */
        }

        .chart-body {
            height: 190px;
            /* Smaller for mobile */
        }
    }

    @media (max-width: 768px) {
        .admin-dashboard-container {
            padding: 12px 8px;
            /* Even smaller padding on mobile */
            width: 100%;
            /* Full width on small screens */
        }

        .stats-cards {
            grid-template-columns: 1fr;
        }

        .stat-card {
            padding: 12px;
        }
    }

    /* Ensure content fits within sidebar layout */
    @media (min-width: 769px) {
        .admin-dashboard-container {
            width: calc(100% - 270px);
            /* Account for sidebar */
            margin-left: 260px;
            /* Match sidebar width */
            transition: margin-left 0.3s ease, width 0.3s ease;
        }

        /* When sidebar is closed */
        .sidebar-closed .admin-dashboard-container {
            width: calc(100% - 30px);
            margin-left: 20px;
        }
    }
</style>