<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Kartu Statistik -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Statistik Pengguna</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-100 p-4 rounded-lg">
                            <p class="text-sm text-blue-800">Total Pengguna</p>
                            <p class="text-3xl font-bold text-blue-900">{{ $totalUsers }}</p>
                        </div>
                        <div class="bg-green-100 p-4 rounded-lg">
                            <p class="text-sm text-green-800">Admin</p>
                            <p class="text-3xl font-bold text-green-900">{{ $totalAdmins }}</p>
                        </div>
                        <div class="bg-yellow-100 p-4 rounded-lg">
                            <p class="text-sm text-yellow-800">User Biasa</p>
                            <p class="text-3xl font-bold text-yellow-900">{{ $totalRegularUsers }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Manajemen Pengguna (Fitur Nilai Plus) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Daftar Pengguna & Manajemen Role</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role Saat Ini</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi Ubah Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hapus User</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($users as $userItem)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $userItem->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $userItem->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $userItem->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if ($userItem->role === 'admin')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Admin
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    User
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <form action="{{ route('admin.users.updateRole', $userItem) }}" method="POST" class="inline-flex items-center space-x-2">
                                                @csrf
                                                @method('PATCH')
                                                <select name="role" class="text-xs border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                    <option value="user" {{ $userItem->role === 'user' ? 'selected' : '' }}>User</option>
                                                    <option value="admin" {{ $userItem->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                </select>
                                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                    Simpan
                                                </button>
                                            </form>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if (auth()->id() !== $userItem->id)
                                                <form action="{{ route('admin.users.destroy', $userItem) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-gray-400 italic">(Akun Anda)</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Belum ada pengguna terdaftar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
