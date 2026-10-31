<?php

add_action( 'init', function () {

	$post_type = get_post_type_object( 'tribe_events' );

	if ( ! $post_type ) {
		return;
	}

	$post_type->template = [
		[
			'tribe/event-datetime',
		],

		[
			'core/group',
			[
				'layout' => [
					'type' => 'flex',
					'flexWrap' => 'nowrap',
					'verticalAlignment' => 'top',
					'justifyContent' => 'space-between',
				],
			],
			[
				[
					'core/group',
					[
						'layout' => [
							'type' => 'constrained',
						],
					],
					[
						[
							'core/paragraph',
						],
						[
							'tribe/event-price',
						],
						[
							'tribe/event-website',
							[
								'urlLabel' => 'Tickets Available Now',
							],
						],
					],
				],

				[
					'core/image',
					[
						'sizeSlug' => 'medium',
						'linkDestination' => 'none',
					],
				],
			],
		],

		[
			'tribe/event-venue',
		],

		[
			'tribe/event-links',
		],
	];

}, 20 );