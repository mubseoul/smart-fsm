<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing categories
        Category::truncate();

        $categories = [
            // HOME SERVICES
            'Appliance Repair' => [
                'description' => 'Professional appliance repair services',
                'subcategories' => [
                    'Washer/Dryer Repair',
                    'Refrigerator Repair',
                    'Dishwasher Service',
                    'Oven Repair'
                ]
            ],
            'Residential Cleaning' => [
                'description' => 'Professional residential cleaning services',
                'subcategories' => [
                    'Deep Cleaning',
                    'Move In/Out Cleaning',
                    'Recurring Maid Service'
                ]
            ],
            'Commercial Cleaning' => [
                'description' => 'Professional commercial cleaning services',
                'subcategories' => [
                    'Office Cleaning',
                    'Janitorial Services',
                    'Post-Construction Cleaning'
                ]
            ],
            'Specialty Cleaning' => [
                'description' => 'Specialized cleaning services',
                'subcategories' => [
                    'Carpet & Upholstery',
                    'Pressure Washing',
                    'Window Cleaning',
                    'Air Duct Cleaning'
                ]
            ],

            // AUTOMOTIVE
            'Mobile Mechanic' => [
                'description' => 'Mobile automotive repair services',
                'subcategories' => [
                    'Oil Change',
                    'Brake Service',
                    'Battery Replacement',
                    'Diagnostics'
                ]
            ],
            'Detailing' => [
                'description' => 'Professional car detailing services',
                'subcategories' => [
                    'Exterior Wash & Wax',
                    'Interior Shampoo',
                    'Ceramic Coating'
                ]
            ],
            'Towing & Roadside Assistance' => [
                'description' => 'Emergency roadside assistance services',
                'subcategories' => [
                    'Jump Start',
                    'Flat Tire Change',
                    'Fuel Delivery'
                ]
            ],
            'Window Tinting & Wraps' => [
                'description' => 'Vehicle customization services',
                'subcategories' => [
                    'Tint Installation',
                    'Vinyl Wraps',
                    'Headlight Restoration'
                ]
            ],

            // BEAUTY & WELLNESS
            'Hair' => [
                'description' => 'Professional hair services',
                'subcategories' => [
                    'Women\'s Haircut',
                    'Men\'s Haircut',
                    'Color & Highlights'
                ]
            ],
            'Nails' => [
                'description' => 'Professional nail services',
                'subcategories' => [
                    'Manicure/Pedicure',
                    'Gel Nails',
                    'Acrylic Sets'
                ]
            ],
            'Massage' => [
                'description' => 'Professional massage therapy services',
                'subcategories' => [
                    'Deep Tissue',
                    'Swedish',
                    'Mobile Massage'
                ]
            ],
            'Aesthetics' => [
                'description' => 'Beauty and aesthetic services',
                'subcategories' => [
                    'Facial Services',
                    'Waxing',
                    'Eyelash Extensions'
                ]
            ],
            'Fitness' => [
                'description' => 'Health and fitness services',
                'subcategories' => [
                    'Personal Training',
                    'Yoga Classes',
                    'Nutrition Coaching'
                ]
            ],

            // BUSINESS SERVICES
            'IT & Tech Support' => [
                'description' => 'Information technology support services',
                'subcategories' => [
                    'Network Setup',
                    'Computer Repair',
                    'Remote Troubleshooting'
                ]
            ],
            'Security Services' => [
                'description' => 'Professional security services',
                'subcategories' => [
                    'Security Camera Installation',
                    'Alarm System Setup',
                    'Access Control'
                ]
            ],
            'Office Setup' => [
                'description' => 'Office installation and setup services',
                'subcategories' => [
                    'Furniture Assembly',
                    'Cubicle Installation',
                    'Cable Management'
                ]
            ],
            'Admin & Support' => [
                'description' => 'Administrative and support services',
                'subcategories' => [
                    'Virtual Assistants',
                    'Bookkeeping',
                    'Notary Services'
                ]
            ],

            // EVENTS
            'Event Planning' => [
                'description' => 'Professional event planning services',
                'subcategories' => [
                    'Corporate Events',
                    'Birthday Parties',
                    'Weddings'
                ]
            ],
            'Party Rentals' => [
                'description' => 'Party equipment rental services',
                'subcategories' => [
                    'Bounce Houses',
                    'Tents & Tables',
                    'Photo Booths'
                ]
            ],
            'Performers' => [
                'description' => 'Entertainment performers for events',
                'subcategories' => [
                    'DJs',
                    'Magicians',
                    'Clowns'
                ]
            ],
            'Food & Beverage' => [
                'description' => 'Food and beverage services for events',
                'subcategories' => [
                    'Bartenders',
                    'Caterers',
                    'Food Trucks'
                ]
            ],

            // OUTDOOR SERVICES
            'Landscaping' => [
                'description' => 'Professional landscaping services',
                'subcategories' => [
                    'Lawn Care',
                    'Tree Trimming',
                    'Sod Installation'
                ]
            ],
            'Fencing & Decks' => [
                'description' => 'Fence and deck construction services',
                'subcategories' => [
                    'Fence Repair',
                    'Deck Staining',
                    'Pergola Builds'
                ]
            ],
            'Pest Control' => [
                'description' => 'Professional pest control services',
                'subcategories' => [
                    'Termite Control',
                    'Rodent Removal',
                    'Lawn Spraying'
                ]
            ],
            'Pool Services' => [
                'description' => 'Swimming pool maintenance services',
                'subcategories' => [
                    'Cleaning & Maintenance',
                    'Equipment Repair',
                    'Resurfacing'
                ]
            ],

            // TRAINING & EDUCATION
            'Trade Skills' => [
                'description' => 'Professional trade skills training',
                'subcategories' => [
                    'Electrical Training',
                    'Plumbing Courses',
                    'HVAC Certification'
                ]
            ],
            'Beauty & Wellness' => [
                'description' => 'Beauty and wellness training courses',
                'subcategories' => [
                    'Nail Technician Training',
                    'Massage Therapy Course',
                    'Hair Styling Academy'
                ]
            ],
            'Automotive Training' => [
                'description' => 'Automotive skills training',
                'subcategories' => [
                    'Auto Repair Basics',
                    'Mobile Detailing Training',
                    'Window Tint Installation'
                ]
            ],
            'Security & Safety' => [
                'description' => 'Security and safety training courses',
                'subcategories' => [
                    'Security Guard License Course',
                    'Self-Defense Classes',
                    'CPR & First Aid Training'
                ]
            ],
            'Business & Marketing' => [
                'description' => 'Business and marketing training',
                'subcategories' => [
                    'How to Start a Cleaning Business',
                    'Digital Marketing for Local Services',
                    'Bookkeeping 101'
                ]
            ],
            'Creative Skills' => [
                'description' => 'Creative skills training',
                'subcategories' => [
                    'Photography Classes',
                    'Graphic Design Training',
                    'Music Lessons'
                ]
            ],

            // LOGISTICS
            'Moving Services' => [
                'description' => 'Professional moving services',
                'subcategories' => [
                    'Local Moving',
                    'Packing/Unpacking',
                    'Loading/Unloading'
                ]
            ],
            'Delivery Services' => [
                'description' => 'Delivery and transportation services',
                'subcategories' => [
                    'Furniture Delivery',
                    'Appliance Delivery',
                    'Junk Removal'
                ]
            ],

            // PET SERVICES
            'Pet Grooming' => [
                'description' => 'Professional pet grooming services',
                'subcategories' => [
                    'Mobile Grooming',
                    'Nail Trimming'
                ]
            ],
            'Pet Boarding' => [
                'description' => 'Pet boarding and care services',
                'subcategories' => [
                    'Dog Boarding',
                    'Cat Boarding'
                ]
            ],
            'Training' => [
                'description' => 'Pet training services',
                'subcategories' => [
                    'Obedience Training',
                    'Behavior Modification'
                ]
            ],
            'Walking & Sitting' => [
                'description' => 'Pet walking and sitting services',
                'subcategories' => [
                    'Daily Walks',
                    'Drop-In Visits'
                ]
            ]
        ];

        foreach ($categories as $categoryName => $categoryData) {
            // Create main category
            $mainCategory = Category::create([
                'name' => $categoryName,
                'slug' => Category::generateSlug($categoryName),
                'description' => $categoryData['description'],
                'parent_id' => 0,
                'active' => true,
                'is_deletable' => true
            ]);

            // Create subcategories
            if (isset($categoryData['subcategories'])) {
                foreach ($categoryData['subcategories'] as $subcategoryName) {
                    Category::create([
                        'name' => $subcategoryName,
                        'slug' => Category::generateSlug($subcategoryName),
                        'description' => $subcategoryName . ' services',
                        'parent_id' => $mainCategory->id,
                        'active' => true,
                        'is_deletable' => true
                    ]);
                }
            }
        }

        $this->command->info('Categories and subcategories seeded successfully!');
        $this->command->info('Total main categories: ' . Category::where('parent_id', 0)->count());
        $this->command->info('Total subcategories: ' . Category::where('parent_id', '>', 0)->count());
    }
} 