@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="row mb-5 text-center">
			<div class="col-md-2">
				<div class="alert alert-primary" role="alert">
					Доступно денег: <strong>{{ $settings->where('key', 'money')->first()->value }}</strong>
				</div>
			</div>
			<div class="col-md-2">
				<div class="alert alert-warning" role="alert">
					Доступно предметов: <strong>{{ $settings->where('key', 'prize')->first()->value }}</strong>
				</div>
			</div>
			<div class="col-md-6"></div>
			<div class="col-md-2">
				<a href="{{ route('userGifts.get') }}" class="btn btn-primary"><span class="">Получить приз!</span></a>
			</div>
		</div>

		<div class="row">
			<div class="col-md-8">
				<table class="table table-striped table-hover">
					<h4>Таблица призов пользователя <strong>{{ auth()->user()->name }}</strong></h4>
					<thead>
					<tr class="text-center">
						<th scope="col">#</th>
						<th scope="col">Тип</th>
						<th scope="col">Сумма / Вещь</th>
						<th scope="col">Дата</th>
						<th scope="col">Статус</th>
						<th scope="col">Действия</th>
					</tr>
					</thead>
					<tbody>
					@foreach($userGifts as $userGift)
						<tr class="text-center">
							<th>{{ $userGift->id }}</th>
							<td>{{ $userGift->giftType->name }}</td>
							<td>{{ $userGift->prize_type_id ? $userGift->prizeType->name : $userGift->value }}</td>
							<td>{{ $userGift->created_at }}</td>
							<td>{{ $userGift->status }}</td>
							@if($userGift->prize_type_id && $userGift->status === 'sent_out')
								<th>Приз уже отправлен</th>
							@elseif(!($userGift->prize_type_id) && auth()->user()->{$userGift->getTypeOfBalance($userGift->giftType->name)} < $userGift->value)
								<th>Недостаточно на счёте</th>
							@elseif(in_array($userGift->status, ['exchanged', 'canceled', 'withdrawn']))
								<th>Недоступны</th>
							@else
								<td>
									@if($userGift->giftType->name === 'money')
										<a
											href="{{ route('userGifts.withdraw', $userGift->id) }}"
											class="btn btn-success"
											title="Вывести"
										>
											<i class="fa fa-arrow-up"></i>
										</a>
										<a
											href="{{ route('userGifts.exchange', $userGift->id) }}"
											class="btn btn-warning"
											title="Сконвертировать"
										>
											<i class="fa phpdebugbar-fa-refresh"></i>
											<strong> 2</strong>
										</a>
									@endif
									<a
										href="{{ route('userGifts.cancel', $userGift->id) }}"
										class="btn btn-danger"
										title="Отказаться"
									>
										<i class="fa fa-times"></i>
									</a>
								</td>
							@endif
						</tr>
					@endforeach
					</tbody>
				</table>

				{{ $userGifts->links() }}
			</div>
			<div class="col-md-2">
				<div class="alert alert-primary" role="alert">
					На денежном счёте: <strong>{{ auth()->user()->money_balance }}</strong>
				</div>
			</div>
			<div class="col-md-2">
				<div class="alert alert-warning" role="alert">
					На бонусном счёте: <strong>{{ auth()->user()->bonus_balance }}</strong>
				</div>
			</div>
		</div>
	</div>
@endsection