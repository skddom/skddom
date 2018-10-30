<script>
    $nc(function () {
        $nc(document).on('change', 'INPUT[name=priority]', function () {
            var $this = $nc(this);
            var priority = parseInt($this.val());
            priority = isNaN(priority) ? 0 : Math.abs(priority);
            $this.val(priority);

            var url = $this.siblings('INPUT[name=url]').val() + priority;

            var priorities = [];

            $this.closest('TABLE').find('TBODY TR').each(function () {
                priorities.push({
                    priority: parseInt($nc(this).find('INPUT[name=priority]').val()),
                    row: $nc(this)
                });
            });

            priorities.sort(function (a, b) {
                return a.priority - b.priority;
            });

            for (var i in priorities) {
                $this.closest('TABLE').find('TBODY').append(priorities[i].row);
            }

            nc.process_start('priority_change');
            $nc.get(url, function () {
                nc.process_stop('priority_change');
            });

            return true;
        });
    });
</script>