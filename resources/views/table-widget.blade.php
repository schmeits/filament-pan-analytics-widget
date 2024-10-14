@php
    $tableHeading = $this->getTableHeading();
    $headers = $this->getHeaders();
    $data = array_slice($this->getData(), 0, $this->getLimit());
    $options = $this->getOptions();
    $hasOptions = count($options) > 0;
@endphp
<x-filament-widgets::widget class="fi-wi-stats-overview">
    <div
        @if ($pollingInterval = $this->getPollingInterval())
            wire:poll.{{ $pollingInterval }}
        @endif
    >
        <div
            class="fi-ta-ctn divide-y divide-gray-200 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10">
            @if ($tableHeading || $hasOptions || $this->hasLimitedResults())
                <div class="fi-ta-header-ctn divide-y divide-gray-200 dark:divide-white/10">
                    <div class="fi-ta-header flex flex-col gap-3 p-4 sm:px-6 sm:flex-row sm:items-center">
                        <div class="grid gap-y-1" wire:ignore>
                            @if ($tableHeading)
                            <h3 class="fi-ta-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white" >
                                {{ $tableHeading }}
                            </h3>
                            @endif
                        </div>
                        <div class="fi-ta-actions flex shrink-0 items-center gap-3 flex-wrap justify-start sm:ms-auto">
                            @if ($this->limitResults)
                                <div class="fi-ac-select-action">
                                    <label for="select" class="sr-only">
                                        Limit
                                    </label>
                                    <div class="fi-input-wrp flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white dark:bg-white/5 [&amp;:not(:has(.fi-ac-action:focus))]:focus-within:ring-2 ring-gray-950/10 dark:ring-white/20 [&amp;:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-600 dark:[&amp;:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-500">
                                        <div class="min-w-0 flex-1">
                                            <select class="fi-select-input block w-full border-none bg-transparent py-1.5 pe-8 text-base text-gray-950 transition duration-75 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] dark:text-white dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] sm:text-sm sm:leading-6 [&amp;_optgroup]:bg-white [&amp;_optgroup]:dark:bg-gray-900 [&amp;_option]:bg-white [&amp;_option]:dark:bg-gray-900 ps-3" id="limit" wire:model.live="limit">
                                                <option value="5">{{ trans("filament-pan-analytics-widget::translations.widget.global.limit", ['count' => 5]) }}</option>
                                                <option value="10">{{ trans("filament-pan-analytics-widget::translations.widget.global.limit", ['count' => 10]) }}</option>
                                                <option value="20">{{ trans("filament-pan-analytics-widget::translations.widget.global.limit", ['count' => 20]) }}</option>
                                                <option value="500">{{ trans("filament-pan-analytics-widget::translations.widget.global.limit_show_all") }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            @endif
            <div class="fi-ta-content relative divide-y divide-gray-200 overflow-x-auto dark:divide-white/10 dark:border-t-white/10">
                <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
                    <thead class="divide-y divide-gray-200 dark:divide-white/5">
                    <tr class="bg-gray-50 dark:bg-white/5">
                        @foreach ($headers as $header)
                            <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-title" style="width:{{ $header['width'] }}">
                            <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
                                <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                    {{ $header['label'] }}
                                </span>
                            </span>
                            </th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                    @foreach ($data as $row)
                        <tr class="fi-ta-row [@media(hover:hover)]:transition [@media(hover:hover)]:duration-75">
                            @foreach ($headers as $header)
                                <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3 fi-table-cell-title">
                                    <div class="fi-ta-col-wrp">
                                        <div class="flex w-full disabled:pointer-events-none justify-start text-start">
                                            <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                                                <div class="flex">
                                                    <div class="flex max-w-max" style="">
                                                        <div class="fi-ta-text-item inline-flex items-center gap-1.5">
                                                    <span class="fi-ta-text-item-label text-sm leading-6 text-gray-950 dark:text-white" style="">
                                                        @php
                                                            $value = $row[$header['name']];
                                                        @endphp
                                                        @if (is_array($value))
                                                            <div class="flex flex-wrap grow-1 gap-1">
                                                            @foreach ($value as $k=>$v)
                                                                <div class="flex gap-1">
                                                                    <x-filament::badge>{{ $k }}</x-filament::badge>
                                                                    <span>{{ $v }}</span>
                                                                </div>
                                                            @endforeach
                                                            </div>
                                                        @else
                                                            {{ $value ?? '' }}
                                                        @endif
                                                    </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
